namespace GreatMarketRealm\Core;

class Updater {

    public function run(): void {

        $installed = get_option('gmrc_db_version', '0.0.0');

        if (version_compare($installed, GMRC_DB_VERSION, '<')) {

            // Run database migrations

            update_option('gmrc_db_version', GMRC_DB_VERSION);

        }

    }

}
