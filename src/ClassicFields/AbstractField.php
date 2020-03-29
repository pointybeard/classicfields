<?php

declare(strict_types=1);

namespace pointybeard\Symphony\Extensions\ClassicFields;

use pointybeard\Helpers\Functions\Flags;
use SymphonyPDO;

abstract class AbstractField implements Interfaces\FieldInterface
{
    protected $extensionDirectory;

    public const STATUS_ENABLED = 'enabled';
    public const STATUS_DISABLED = 'disabled';

    public const FLAG_NONE = 0x0000;
    public const FLAG_SKIP_CHECKS = 0x0001;
    public const FLAG_FORCE = 0x0002;
    public const FLAG_DROP_TABLES = 0x0004;

    public function __construct(?string $extensionDirectory = null)
    {
        $this->extensionDirectory = $extensionDirectory ?? realpath(__DIR__.'/../../');
    }

    public function name(): string
    {
        $class = new \ReflectionClass(static::class);

        return strtolower($class->getShortName());
    }

    public function status(): string
    {
        return file_exists($this->path())
            ? self::STATUS_ENABLED
            : self::STATUS_DISABLED
        ;
    }

    public function install(int $flags = null): void
    {
        if (self::STATUS_ENABLED == $this->status() && false == Flags\is_flag_set($flags, self::FLAG_FORCE)) {
            return;
        }

        SymphonyPDO\Loader::instance()->query(
            static::getCreateFieldSQL()
        );

        static::enable();
    }

    public function uninstall(int $flags = null): void
    {
        // Check where this field is being used
        if (false == Flags\is_flag_set($flags, self::FLAG_SKIP_CHECKS) && null != $sections = static::getUsedBy()) {
            throw new Exceptions\FieldStillInUseException($this->name(), $sections);
        }

        static::disable();

        if (true == Flags\is_flag_set($flags, self::FLAG_DROP_TABLES)) {
            $query = SymphonyPDO\Loader::instance()->query(static::getFropFieldSQL());
        }
    }

    public function getFropFieldSQL(): string
    {
        return "DROP TABLE IF EXISTS `tbl_fields_{$this->name()}`";
    }

    public function getUsedBy(): ?array
    {
        $query = SymphonyPDO\Loader::instance()->query(
            "SELECT DISTINCT s.name FROM `tbl_fields` as `f` LEFT JOIN `tbl_sections` as `s` ON f.parent_section = s.id WHERE f.type = '{$this->name()}' ORDER BY s.name ASC;"
        );

        $sections = $query->fetchAll(\PDO::FETCH_COLUMN, 0);

        return false != $sections ? $sections : null;
    }

    public function enable(int $flags = null): void
    {
        if (self::STATUS_ENABLED == $this->status() && false == Flags\is_flag_set($flags, self::FLAG_FORCE)) {
            return;
        }

        try {
            $cwd = getcwd();
            chdir($this->extensionDirectory.'/fields');
            self::createSymbolicLink(
                "../src/Fields/field.{$this->name()}.php",
                "field.{$this->name()}.php",
                $flags
            );
            chdir($cwd);
        } catch (Exceptions\SymlinkExistsException | Exceptions\SymlinkExistsException $ex) {
            throw new Exceptions\EnablingFieldFailedException($this->name(), $ex->getMessage());
        }

        // Some extensions explicity require the field from TOOLKIT/fields so
        // lets check if the file exists and if not add a symlink there too
        if (false == file_exists(TOOLKIT."/fields/field.{$this->name()}.php") || true == is_link(TOOLKIT."/fields/field.{$this->name()}.php")) {
            try {
                $cwd = getcwd();
                chdir(TOOLKIT.'/fields');
                self::createSymbolicLink(
                    "../../../../extensions/classicfields/src/Fields/field.{$this->name()}.php",
                    "field.{$this->name()}.php",
                    $flags
                );
                chdir($cwd);
            } catch (Exceptions\SymlinkExistsException | Exceptions\SymlinkExistsException $ex) {
                throw new Exceptions\EnablingFieldFailedException($this->name(), $ex->getMessage());
            }
        }
    }

    public function disable(int $flags = null): void
    {
        if (self::STATUS_ENABLED != $this->status()) {
            return;
        }

        // Check where this field is being used
        if (false == Flags\is_flag_set($flags, self::FLAG_SKIP_CHECKS) && null != $sections = static::getUsedBy()) {
            throw new Exceptions\FieldStillInUseException($this->name(), $sections);
        }

        if (false == unlink($this->path())) {
            throw new Exceptions\DisablingFieldFailedException($this->name(), 'Unable to delete symbolic link '.$this->path());
        }

        // Check if the file in TOOLKIT/fields is a symlink, in which case we need to remove it as well
        if (true == is_link(TOOLKIT."/fields/field.{$this->name()}.php") && false == unlink(TOOLKIT."/fields/field.{$this->name()}.php")) {
            throw new Exceptions\DisablingFieldFailedException($this->name(), 'Unable to delete symbolic link '.TOOLKIT."/fields/field.{$this->name()}.php");
        }
    }

    public function path(): string
    {
        return $this->extensionDirectory."/fields/field.{$this->name()}.php";
    }

    protected static function createSymbolicLink(string $target, ?string $destination, int $flags = null): bool
    {
        if (false == Flags\is_flag_set($flags, self::FLAG_FORCE) && null !== $destination && true == file_exists($destination)) {
            throw new Exceptions\SymlinkExistsException($destination);
        }

        if (false == realpath($target)) {
            throw new Exceptions\SymlinkTargetMissingException($target);
        }

        $command = sprintf(
            'ln -vs%s %s %s %s',
            true == Flags\is_flag_set($flags, self::FLAG_FORCE)
                ? 'f'
                : null,
            $target,
            $destination,
            null !== $destination
                ? '-T'
                : null
        );

        exec($command, $output, $return);

        if (true != file_exists($destination)) {
            throw new Exceptions\SymlinkCreationFailedException($destination, 'Target symlink could not be created not exist. Check permissions on target directory.');
        }

        return true;
    }
}
