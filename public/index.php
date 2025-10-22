<?php

require "../vendor/autoload.php";

session_start();

use app\library\Authenticate;
use app\library\GoogleClient;

$googleClient = new GoogleClient;
$googleClient->init();

$auth = new Authenticate;
if ($googleClient->authorized()) {
  $auth->authGoogle($googleClient->getData());
}

if (isset($_GET['logout'])) {
  $auth->logout();
}

$authUrl = $googleClient->generateAuthLink();

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

function render_header($title = 'App')
{
  echo "<!doctype html>\n<html lang=\"pt-br\">\n<head>\n<meta charset=\"utf-8\">\n<meta name=\"viewport\" content=\"width=device-width,initial-scale=1\">\n<title>" . htmlspecialchars($title) . "</title>\n<link rel=\"stylesheet\" href=\"/styles.css\">\n</head>\n<body>\n<div class=\"container\">\n";
}

function render_footer()
{
  echo "</div>\n</body>\n</html>\n";
}

if ($path === '/home') {
  // protected page
  if (!isset($_SESSION['auth']) || !$_SESSION['auth']) {
    header('Location: /');
    exit;
  }

  render_header('Home');
  $user = $_SESSION['user'];
  ?>
  <main class="card">
    <div class="profile">
      <img src="<?php echo htmlspecialchars($user->picture ?? '/placeholder.png'); ?>" alt="Foto" />
      <h1><?php echo htmlspecialchars($user->firstName . ' ' . $user->lastName); ?></h1>
      <p><?php echo htmlspecialchars($user->email); ?></p>
    </div>
    <div class="actions">
      <a class="btn" href="/?logout=true">Logout</a>
    </div>
  </main>
  <?php
  render_footer();
  exit;

} else {
  render_header('Login');
  ?>
  <main class="card">
    <h1>Bem-vindo</h1>
    <p>Faça login com sua conta Google ou comcredenciais locais.</p>

    <?php if (isset($_SESSION['user'], $_SESSION['auth'])): ?>
      <div class="logged">
        <strong><?php echo htmlspecialchars($_SESSION['user']->firstName . ' ' . $_SESSION['user']->lastName); ?></strong>
        <a class="btn small" href="/home">Ir para Home</a>
        <a class="btn outline" href="?logout=true">Logout</a>
      </div>
    <?php else: ?>
      <form class="form" method="post" action="/">
        <label>
          <span>Email</span>
          <input type="email" name="email" placeholder="seu@exemplo.com">
        </label>
        <label>
          <span>Senha</span>
          <input type="password" name="password" placeholder="••••••">
        </label>
        <div class="form-actions">
          <button class="btn" type="submit">Entrar</button>
          <a class="btn outline" href="<?php echo $authUrl; ?>">Entrar com Google</a>
        </div>
      </form>
    <?php endif; ?>

    <p class="muted">Ainda não implementado: autenticação local e registro.</p>
  </main>
  <?php
  render_footer();
}
