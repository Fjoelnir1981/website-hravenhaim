<?php
function page_start(string $title): void { ?>
<!doctype html><html lang="de"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?= e($title) ?> · Hravenhaim Sippe</title><link rel="stylesheet" href="css/main.css"><script src="js/main.js" defer></script></head><body><?php readfile(__DIR__.'/header.html'); ?><main id="inhalt"><div class="page-heading"><span class="eyebrow">HRAVENHAIM SIPPE</span><h1><?= e($title) ?></h1></div><div class="page-content"><?php }
function page_end(): void { ?></div></main><?php readfile(__DIR__.'/footer.html'); ?></body></html><?php }
