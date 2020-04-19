<?php

/**
 * Checkbox field simulates a HTML checkbox field, in that it represents a
 * simple yes/no field.
 */
if(false == class_exists('FieldCheckbox')) {
class FieldCheckbox extends Field implements ExportableField, ImportableField
{
    public function __construct()
    {
        parent::__construct();
        $this->_name = __('Checkbox');
        $this->_required = true;

        $this->set('required', 'no');
        $this->set('location', 'sidebar');
    }

    /*-------------------------------------------------------------------------
        Definition:
    -------------------------------------------------------------------------*/

    public function canToggle()
    {
        return true;
    }

    public function getToggleStates()
    {
        return array(
            'yes' => __('Yes'),
            'no' => __('No'),
        );
    }

    public function toggleFieldData(array $data, $newState, $entry_id = null)
    {
        $data['value'] = $newState;

        return $data;
    }

    public function canFilter()
    {
        return true;
    }

    public function isSortable()
    {
        return true;
    }

    public function allowDatasourceOutputGrouping()
    {
        return true;
    }

    public function allowDatasourceParamOutput()
    {
        return true;
    }

    public function fetchFilterableOperators()
    {
        return array(
            array(
                'title' => 'is',
                'filter' => ' ',
                'help' => __('Find values that are an exact match for the given string.'),
            ),
        );
    }

    public function fetchSuggestionTypes()
    {
        return array('static');
    }

    /*-------------------------------------------------------------------------
        Setup:
    -------------------------------------------------------------------------*/

    public function createTable()
    {
        return Symphony::Database()->query(
            'CREATE TABLE IF NOT EXISTS `tbl_entries_data_'.$this->get('id')."` (
              `id` int(11) unsigned NOT null auto_increment,
              `entry_id` int(11) unsigned NOT null,
              `value` enum('yes','no') NOT null default '".('on' == $this->get('default_state') ? 'yes' : 'no')."',
              PRIMARY KEY  (`id`),
              UNIQUE KEY `entry_id` (`entry_id`),
              KEY `value` (`value`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
        );
    }

    /*-------------------------------------------------------------------------
        Settings:
    -------------------------------------------------------------------------*/

    public function findDefaults(array &$settings)
    {
        if (!isset($settings['default_state'])) {
            $settings['default_state'] = 'off';
        }
    }

    public function displaySettingsPanel(XMLElement &$wrapper, $errors = null)
    {
        parent::displaySettingsPanel($wrapper, $errors);

        // Checkbox Default State
        $label = Widget::Label();
        $label->setAttribute('class', 'column');
        $input = Widget::Input('fields['.$this->get('sortorder').'][default_state]', 'on', 'checkbox');

        if ('on' == $this->get('default_state')) {
            $input->setAttribute('checked', 'checked');
        }

        $label->setValue(__('%s Checked by default', array($input->generate())));
        $wrapper->appendChild($label);

        // Requirements and table display
        $this->appendStatusFooter($wrapper);
    }

    public function commit()
    {
        if (!parent::commit()) {
            return false;
        }

        $id = $this->get('id');

        if (false === $id) {
            return false;
        }

        $fields = array();

        $fields['default_state'] = ($this->get('default_state') ? $this->get('default_state') : 'off');

        return FieldManager::saveSettings($id, $fields);
    }

    /*-------------------------------------------------------------------------
        Publish:
    -------------------------------------------------------------------------*/

    public function displayPublishPanel(XMLElement &$wrapper, $data = null, $flagWithError = null, $fieldnamePrefix = null, $fieldnamePostfix = null, $entry_id = null)
    {
        if (!$data) {
            // TODO: Don't rely on $_POST
            if (isset($_POST) && !empty($_POST)) {
                $value = 'no';
            } elseif ('on' == $this->get('default_state')) {
                $value = 'yes';
            } else {
                $value = 'no';
            }
        } else {
            $value = ('yes' === $data['value'] ? 'yes' : 'no');
        }

        $label = Widget::Label();

        if ('yes' !== $this->get('required')) {
            $label->appendChild(new XMLElement('i', __('Optional')));
        }

        $input = Widget::Input('fields'.$fieldnamePrefix.'['.$this->get('element_name').']'.$fieldnamePostfix, 'yes', 'checkbox', ('yes' === $value ? array('checked' => 'checked') : null));

        $label->setValue($input->generate(false).' '.$this->get('label'));

        if (null != $flagWithError) {
            $wrapper->appendChild(Widget::Error($label, $flagWithError));
        } else {
            $wrapper->appendChild($label);
        }
    }

    public function checkPostFieldData($data, &$message, $entry_id = null)
    {
        $message = null;

        // Check if any value was passed
        $has_no_value = is_array($data) ? empty($data) : 0 == strlen(trim($data));
        // Check that the value passed was 'on' or 'yes', if it's not
        // then the field has 'no value' in the context of being required. RE: #1569
        $has_no_value = (false === $has_no_value) ? !in_array(strtolower($data), array('on', 'yes')) : true;

        if ('yes' === $this->get('required') && $has_no_value) {
            $message = __('‘%s’ is a required field.', array($this->get('label')));

            return self::__MISSING_FIELDS__;
        }

        return self::__OK__;
    }

    public function processRawFieldData($data, &$status, &$message = null, $simulate = false, $entry_id = null)
    {
        $status = self::__OK__;

        return array(
            'value' => ('yes' === strtolower($data) || 'on' == strtolower($data) || true === $data ? 'yes' : 'no'),
        );
    }

    /*-------------------------------------------------------------------------
        Output:
    -------------------------------------------------------------------------*/

    public function appendFormattedElement(XMLElement &$wrapper, $data, $encode = false, $mode = null, $entry_id = null)
    {
        $value = ('yes' === $data['value'] ? 'Yes' : 'No');

        $wrapper->appendChild(new XMLElement($this->get('element_name'), ($encode ? General::sanitize($value) : $value)));
    }

    public function prepareTextValue($data, $entry_id = null)
    {
        return $this->prepareExportValue($data, ExportableField::VALUE, $entry_id);
    }

    public function getParameterPoolValue(array $data, $entry_id = null)
    {
        return $this->prepareExportValue($data, ExportableField::POSTDATA, $entry_id);
    }

    /*-------------------------------------------------------------------------
        Import:
    -------------------------------------------------------------------------*/

    public function getImportModes()
    {
        return array(
            'getValue' => ImportableField::STRING_VALUE,
            'getPostdata' => ImportableField::ARRAY_VALUE,
        );
    }

    public function prepareImportValue($data, $mode, $entry_id = null)
    {
        $status = $message = null;
        $modes = (object) $this->getImportModes();
        $value = $this->processRawFieldData($data, $status, $message, true, $entry_id);

        if ($mode === $modes->getValue) {
            return $value['value'];
        } elseif ($mode === $modes->getPostdata) {
            return $value;
        }

        return null;
    }

    /*-------------------------------------------------------------------------
        Export:
    -------------------------------------------------------------------------*/

    /**
     * Return a list of supported export modes for use with `prepareExportValue`.
     *
     * @return array
     */
    public function getExportModes()
    {
        return array(
            'getBoolean' => ExportableField::BOOLEAN,
            'getValue' => ExportableField::VALUE,
            'getPostdata' => ExportableField::POSTDATA,
        );
    }

    /**
     * Give the field some data and ask it to return a value using one of many
     * possible modes.
     *
     * @param mixed $data
     * @param int   $mode
     * @param int   $entry_id
     *
     * @return string|bool|null
     */
    public function prepareExportValue($data, $mode, $entry_id = null)
    {
        $modes = (object) $this->getExportModes();

        // Export unformatted:
        if ($mode === $modes->getPostdata) {
            return
                isset($data['value'])
                && 'yes' === $data['value']
                    ? 'yes'
                    : 'no'
            ;

        // Export formatted:
        } elseif ($mode === $modes->getValue) {
            return
                isset($data['value'])
                && 'yes' === $data['value']
                    ? __('Yes')
                    : __('No')
            ;

        // Export boolean:
        } elseif ($mode === $modes->getBoolean) {
            return
                isset($data['value'])
                && 'yes' === $data['value']
            ;
        }

        return null;
    }

    /*-------------------------------------------------------------------------
        Filtering:
    -------------------------------------------------------------------------*/

    public function displayFilteringOptions(XMLElement &$wrapper)
    {
        $existing_options = array('yes', 'no');

        if (is_array($existing_options) && !empty($existing_options)) {
            $optionlist = new XMLElement('ul');
            $optionlist->setAttribute('class', 'tags');
            $optionlist->setAttribute('data-interactive', 'data-interactive');

            foreach ($existing_options as $option) {
                $optionlist->appendChild(new XMLElement('li', $option));
            }

            $wrapper->appendChild($optionlist);
        }
    }

    public function buildDSRetrievalSQL($data, &$joins, &$where, $andOperation = false)
    {
        $field_id = $this->get('id');
        $default_state = ('on' == $this->get('default_state')) ? 'yes' : 'no';

        if ($andOperation) {
            foreach ($data as $value) {
                ++$this->_key;
                $value = $this->cleanValue($value);
                $joins .= "
                    LEFT JOIN
                        `tbl_entries_data_{$field_id}` AS t{$field_id}_{$this->_key}
                        ON (e.id = t{$field_id}_{$this->_key}.entry_id)
                ";

                if ($default_state == $value) {
                    $where .= "
                        AND (
                            t{$field_id}_{$this->_key}.value = '{$value}'
                            OR
                            t{$field_id}_{$this->_key}.value IS null
                        )
                    ";
                } else {
                    $where .= "
                        AND (t{$field_id}_{$this->_key}.value = '{$value}')
                    ";
                }
            }
        } else {
            if (!is_array($data)) {
                $data = array($data);
            }

            foreach ($data as &$value) {
                $value = $this->cleanValue($value);
            }

            ++$this->_key;
            $data = implode("', '", $data);
            $joins .= "
                LEFT JOIN
                    `tbl_entries_data_{$field_id}` AS t{$field_id}_{$this->_key}
                    ON (e.id = t{$field_id}_{$this->_key}.entry_id)
            ";

            if (false !== strpos($data, $default_state)) {
                $where .= "
                    AND (
                        t{$field_id}_{$this->_key}.value IN ('{$data}')
                        OR
                        t{$field_id}_{$this->_key}.value IS null
                    )
                ";
            } else {
                $where .= "
                    AND (t{$field_id}_{$this->_key}.value IN ('{$data}'))
                ";
            }
        }

        return true;
    }

    /*-------------------------------------------------------------------------
        Sorting:
    -------------------------------------------------------------------------*/

    public function buildSortingSQL(&$joins, &$where, &$sort, $order = 'ASC')
    {
        if ($this->isRandomOrder($order)) {
            $sort = 'ORDER BY RAND()';
        } else {
            $sort = sprintf(
                'ORDER BY (
                    SELECT %s
                    FROM tbl_entries_data_%d AS `ed`
                    WHERE entry_id = e.id
                ) %s, `e`.`id` %s',
                '`ed`.value',
                $this->get('id'),
                $order,
                $order
            );
        }
    }

    public function buildSortingSelectSQL($sort, $order = 'ASC')
    {
        return null;
    }

    /*-------------------------------------------------------------------------
        Grouping:
    -------------------------------------------------------------------------*/

    public function groupRecords($records)
    {
        if (!is_array($records) || empty($records)) {
            return;
        }

        $groups = array($this->get('element_name') => array());

        foreach ($records as $r) {
            $data = $r->getData($this->get('id'));

            $value = $data['value'];

            if (!isset($groups[$this->get('element_name')][$value])) {
                $groups[$this->get('element_name')][$value] = array(
                    'attr' => array('value' => $value),
                    'records' => array(),
                    'groups' => array(),
                );
            }

            $groups[$this->get('element_name')][$value]['records'][] = $r;
        }

        return $groups;
    }

    /*-------------------------------------------------------------------------
        Events:
    -------------------------------------------------------------------------*/

    public function getExampleFormMarkup()
    {
        $label = Widget::Label($this->get('label'));
        $label->appendChild(Widget::Input('fields['.$this->get('element_name').']', 'yes', 'checkbox', ('on' == $this->get('default_state') ? array('checked' => 'checked') : null)));

        return $label;
    }
}
}
