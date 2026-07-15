namespace GreatMarketRealm\Models;

class Character
{
    public ?int $id = null;

    public int $user_id;

    public string $name = '';

    public string $race = '';

    public string $class = '';

    public int $level = 1;

    public string $created_at = '';

    public string $updated_at = '';
}
