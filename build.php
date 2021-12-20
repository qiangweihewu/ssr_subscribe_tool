<?php
require 'vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;

$dotEnv = new Dotenv();
$dotEnv->load(__DIR__.'/.env');

$domain = getenv('DOMAIN');
$accessToken = getenv('ACCESS_TOKEN');

if(($accessToken && ($accessToken != '$ACCESS_TOKEN')) && ($domain && ($domain != '$DOMAIN'))){
    // add subscribe form data to node.txt
    $netlify = new \Yangyao\SSR\Netlify($accessToken,$domain);
    $site = $netlify->getSite();
    $subscribes = $netlify->getSubmissions( $site['id'],'subscribe');
    collect($subscribes)->each(function($subscribe){
        $link = $subscribe['data']['link'];
        $plain = \Yangyao\SSR\SSR::getFromSubLink($link);
        file_put_contents('node.txt',PHP_EOL."## {$link} ".PHP_EOL,FILE_APPEND);
        file_put_contents('node.txt',$plain.PHP_EOL,FILE_APPEND);
    });
}

$plain = file_get_contents('node.txt');
$ssr = collect(explode(PHP_EOL,$plain))->filter(function($line){
   // return \Illuminate\Support\Str::contains($line,"ssr://");
   // include ss, vmess, trojan (vmess has ss in it)
return \Illuminate\Support\Str::contains($1ine, ["ss://", "ssr://", "trojan://"]);
});

return file_put_contents('dist/index.html',base64_encode(implode(PHP_EOL,$ssr->all())));
