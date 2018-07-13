<?php
namespace Acts\CamdramAdminBundle\Composer;

use Composer\Script\Event;
use Symfony\Component\Process\PhpExecutableFinder;
use Sensio\Bundle\DistributionBundle\Composer\ScriptHandler as SensioScriptHandler;

class ScriptHandler extends SensioScriptHandler 
{
    /**
     * Create/refresh the development database
     *
     * @param Event $event
     */
    public static function refreshDatabase(Event $event)
    {
        if (!$event->isDevMode()){
            return;
        }

        $options = self::getOptions($event);
        $consoleDir = self::getConsoleDir($event, 'refresh the database');

        if (null === $consoleDir) {
            return;
        }

        static::writeHeader($event, "Creating SQLite database for development with test fixtures");

        static::executeCommand($event, $consoleDir, 'camdram:database:refresh', $options['process-timeout']);
    }

    /**
     * Download the development assets
     *
     * @param Event $event
     */
    public static function downloadAssets(Event $event)
    {
        if (!$event->isDevMode()){
            return;
        }

        $options = self::getOptions($event);
        $consoleDir = self::getConsoleDir($event, 'download assets');

        if (null === $consoleDir) {
            return;
        }

        static::writeHeader($event, "Downloading compiled JS/CSS assets");

        static::executeCommand($event, $consoleDir, 'camdram:assets:download', $options['process-timeout']);
    }

    public static function welcomeMessage(Event $event)
    {
        static::writeHeader($event, "Complete!");
        
        $event->getIO()->write(<<<'EOF'
Welcome to your Camdram checkout!

Run <options=bold;fg=yellow>php app/console server:run</> to start Camdram on PHP's built-in web server.
The homepage can then be viewed at <options=bold;fg=yellow>http://127.0.0.1:8000/</>

<options=bold;fg=yellow>Database</>
A local SQLite database has been created automatically.
If you are planning on making changes to Camdram's data model however, it is recommended to set up a 
dedicated MySQL database instead.
Visit https://github.com/camdram/camdram/wiki/Setting-up-a-MySQL-database to find out more

<options=bold;fg=yellow>Javascript/CSS</>
Minified assets have been downloaded from https://development.camdram.net/.
If you are planning on doing frontend development you will need to configure the Webpack toolchain.
Visit https://github.com/camdram/camdram/wiki/Webpack-setup-guide to find out more

<options=bold;fg=yellow>Search</>
Search functionality is currently disabled - additional steps are required to set up a search index.
Visit https://github.com/camdram/camdram/wiki/Elasticsearch-setup-guide to find out more.

<options=bold;fg=yellow>Tests</>
You can execute <options=bold;fg=yellow>./runtests --tags ~@search</> to run the automated test suite.
The arguments can be ommitted if Elasticsearch is installed and configured.

<options=bold;fg=yellow>Useful resources</>
 * https://gitter.im/camdram/development - Chat with a developer
 * https://github.com/camdram/camdram/wiki - Information about the codebase
 * https://symfony.com/doc/3.4/ - Find out more about the Symfony web framework

Break a leg!

EOF
        );
    }

    protected static function writeHeader(Event $event, $text)
    {
        $event->getIO()->write("<fg=black;bg=yellow>\n\n"
        ."<fg=black;bg=yellow;options=bold>Camdram Setup:</> " . $text
        ."\n</>\n");
    }
}