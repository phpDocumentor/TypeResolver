workflow "Qa workflow" {
  on = "push"
  resolves = [
    "PHPStan",
    "composer-require-checker",
    "Code style check",
  ]
}

action "composer" {
  uses = "docker://composer"
  secrets = ["GITHUB_TOKEN"]
  args = "install --no-interaction --prefer-dist --optimize-autoloader"
}

action "PHPStan" {
  uses = "docker://oskarstark/phpstan-ga"
  args = "analyse src tests --level max --configuration phpstan.neon"
  secrets = ["GITHUB_TOKEN"]
  needs = ["composer"]
}

action "composer-require-checker" {
  uses = "docker://phpga/composer-require-checker-ga"
  secrets = ["GITHUB_TOKEN"]
  args = "check --config-file ./composer-require-config.json composer.json"
  needs = ["composer"]
}

action "Code style check" {
  uses = "docker://oskarstark/phpcs-ga"
  secrets = ["GITHUB_TOKEN"]
  args = "-d memory_limit=1024M"
  needs = ["composer"]
}
