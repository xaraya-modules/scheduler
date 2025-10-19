<?php

/**
 * Scheduler Module
 *
 * @package modules
 * @subpackage scheduler module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/189.html
 * @author mikespub
 */

namespace Xaraya\Modules\Scheduler;

class Version
{
    /**
     * Get module version information
     *
     * @return array<string, mixed>
     */
    public function __invoke(): array
    {
        return [
            'name' => 'Scheduler',
            'id' => '189',
            'version' => '2.4.1',
            'displayname' => 'Scheduler',
            'description' => 'Schedule Xaraya jobs at certain times of the day/week/month (cron)',
            'credits' => '',
            'help' => '',
            'changelog' => '',
            'license' => '',
            'official' => true,
            'author' => 'mikespub',
            'contact' => 'http://www.xaraya.com/',
            'admin' => true,
            'user' => false,
            'class' => 'Utility',
            'category' => 'Miscellaneous',
            'namespace' => 'Xaraya\\Modules\\Scheduler',
            'twigtemplates' => true,
            'dependencyinfo'
             => [
                 0
                  => [
                      'name' => 'Xaraya Core',
                      'version_ge' => '2.4.1',
                  ],
             ],
        ];
    }
}
