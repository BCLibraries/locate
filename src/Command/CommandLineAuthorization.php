<?php


namespace App\Command;

/**
 * Is user authorized to use CLI commands?
 *
 * Currently authorization is determined by UNIX group
 *
 * @package App\Command
 */
class CommandLineAuthorization
{
    /** @var string */
    private $authorized_group;

    public function __construct(string $authorized_group = "staff")
    {
        $this->authorized_group = $authorized_group;
    }

    /**
     * Is the user authorized to use the CLI?
     *
     * @return bool
     */
    public function isAuthorized(): bool
    {
        // Scroll through all of the user's groups. Return true if there is a match
        // with an approved group.
        foreach (posix_getgroups() as $gid) {
            ['name' => $group_name] = posix_getgrgid($gid);
            if ($this->authorized_group === $group_name) {
                return true;
            }
        }
        return false;
    }
}