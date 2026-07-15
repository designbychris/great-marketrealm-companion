namespace GreatMarketRealm\Database;

class DatabaseManager
{
    protected \wpdb $wpdb;

    public function __construct()
    {
        global $wpdb;

        $this->wpdb = $wpdb;
    }

    public function table(string $table): string
    {
        return $this->wpdb->prefix . "gmr_" . $table;
    }

    public function getWpdb(): \wpdb
    {
        return $this->wpdb;
    }
}
