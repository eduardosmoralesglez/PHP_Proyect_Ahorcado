<?php
declare(strict_types=1);

use App\{Game, WordProvider, Storage, Renderer};

require __DIR__ . '/../vendor/autoload.php';

$storage = new Storage();
$provider = new WordProvider(__DIR__ . '/../data/words.txt');
$renderer = new Renderer();

$state = $storage->get('state');
$word  = $storage->get('word');

if (!$word) {
    $word = $provider->randomWord();
    $storage->set('word', $word);
}

$maxAttempts = 6;
$game = new Game($word, $maxAttempts, $state);

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (isset($_POST['letter'])) {
        $game->guessLetter($_POST['letter']);
    }
    if (isset($_POST['action']) && $_POST['action'] === 'reset') {
        $storage->reset();
        header("Location: /");
        exit;
    }
    $storage->set('state', $game->toState());
}

$masked   = $game->getMaskedWord();
$used     = implode(', ', $game->getUsedLetters());
$attempts = $game->getAttemptsLeft();
$status   = $game->isWon() ? '🎉 ¡Has ganado!' : ($game->isLost() ? '💀 Has perdido' : 'En juego');

?><!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Ahorcado</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body { font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial; margin: 2rem; }
    .card { max-width: 700px; margin: auto; border: 1px solid #e5e7eb; border-radius: 12px; padding: 1.5rem; }
    .row { display: flex; gap: 2rem; align-items: start; }
    .ascii { min-width: 220px; }
    .masked { font-size: 2rem; letter-spacing: .2rem; }
    .status { font-weight: bold; margin: .5rem 0 1rem; }
    .letters input[type=text] { width: 3rem; text-align: center; font-size: 1.25rem; }
    .used { color: #6b7280; }
    button { padding: .5rem .75rem; border-radius: 8px; border: 1px solid #e5e7eb; background: #111827; color: white; cursor: pointer; }
    button.secondary { background: white; color: #111827; }
    form { display: inline-block; margin-right: .5rem; }
  </style>
</head>
<body>
  <div class="card">
    <h1>Juego del Ahorcado</h1>
    <div class="row">
      <div class="ascii"><?= $renderer->ascii($attempts) ?></div>
      <div>
        <div class="masked"><?= htmlspecialchars($masked) ?></div>
        <div class="status"><?= $status ?><?= $game->isLost() ? " — La palabra era: <strong>{$game->getWord()}</strong>" : "" ?></div>

        <?php if (!$game->isWon() && !$game->isLost()): ?>
          <form method="post" class="letters" autocomplete="off">
            <input type="text" name="letter" maxlength="1" required autofocus>
            <button type="submit">Probar letra</button>
          </form>
        <?php endif; ?>

        <form method="post">
          <input type="hidden" name="action" value="reset">
          <button class="secondary" type="submit">Nueva partida</button>
        </form>

        <p class="used"><strong>Letras usadas:</strong> <?= htmlspecialchars($used) ?: '—' ?></p>
        <p><strong>Intentos restantes:</strong> <?= $attempts ?></p>
      </div>
    </div>
  </div>
</body>
</html>
