<?php namespace OpenCFP\Provider;

use Silex\Application;
use Ciconia\Ciconia;
use Silex\ServiceProviderInterface;
use Silex\Provider\TwigServiceProvider as SilexTwigServiceProvider;
use Aptoma\Twig\Extension\MarkdownExtension;
use Ciconia\Extension\Gfm\WhiteSpaceExtension;
use Ciconia\Extension\Gfm\InlineStyleExtension;
use Twig_Extension_Debug;
use Twig_SimpleFunction;

class TwigServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Application $app)
    {
        $app->register(new SilexTwigServiceProvider(), [
            'twig.path' => $app->templatesPath(),
            'twig.options' => [
                'debug' => !$app->isProduction(),
                'cache' => $app->config('cache.enabled') ? $app->cacheTwigPath() : false
            ]
        ]);

        if (!$app->isProduction()) {
            $app['twig']->addExtension(new Twig_Extension_Debug);
        }

        $app['twig']->addFunction(new Twig_SimpleFunction('uploads', function ($path) {
            return '/uploads/' . $path;
        }));

        $app['twig']->addFunction(new Twig_SimpleFunction('assets', function ($path) {
            return '/assets/' . $path;
        }));

        $app['twig']->addGlobal('site', $app->config('application'));

        // Twig Markdown Extension
        $markdown = new Ciconia();
        $markdown->addExtension(new InlineStyleExtension);
        $markdown->addExtension(new WhiteSpaceExtension);
        $engine = new CiconiaEngine($markdown);

        $app['twig']->addExtension(new MarkdownExtension($engine));
    }

    /**
     * {@inheritdoc}
     */
    public function boot(Application $app)
    {
    }
}
