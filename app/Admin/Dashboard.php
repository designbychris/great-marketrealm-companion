namespace GreatMarketRealm\Admin;

class Dashboard
{
    public function render(): void
    {
        $stats = [
            'plugin_version' => GMRC_VERSION,
            'database_version' => get_option('gmrc_db_version'),
            'characters' => 0,
        ];

        include GMRC_PLUGIN_DIR . 'templates/admin/dashboard.php';
    }
}
