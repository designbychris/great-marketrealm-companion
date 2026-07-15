$repo = new CharacterRepository();

$characters = $repo->findByUser(5);

$repo->create($character);

$repo->update($character);

$repo->delete($id);
