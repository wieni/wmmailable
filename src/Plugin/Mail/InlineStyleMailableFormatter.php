<?php

namespace Drupal\wmmailable\Plugin\Mail;

use Drupal\Core\Asset\AssetResolverInterface;
use Drupal\Core\Asset\AttachedAssets;
use Drupal\Core\File\FileSystem;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\wmmailable\MailableInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

/**
 * Provides a plugin to format mails
 *
 * @Mail(
 *   id = "mailable_inline_style",
 *   label = @Translation("Mailable - With inline styles"),
 *   description = @Translation("Mail formatter which converts attached css to inline styles.")
 * )
 */
class InlineStyleMailableFormatter extends PlainMailableFormatter
{
    /** @var FileSystem */
    protected $fileSystem;
    /** @var AssetResolverInterface */
    protected $assetResolver;

    public function __construct(
        MailManagerInterface $mailManager,
        RendererInterface $renderer,
        FileSystem $fileSystem,
        AssetResolverInterface $assetResolver
    ) {
        parent::__construct($mailManager, $renderer);
        $this->fileSystem = $fileSystem;
        $this->assetResolver = $assetResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function format(array $message)
    {
        $message = parent::format($message);
        /** @var MailableInterface $mailable */
        $mailable = $message['mailable'] ?? null;

        $css = null;
        if ($mailable instanceof MailableInterface) {
            $css = implode(PHP_EOL, $this->getCss($mailable));
        }

        $message['body'] = (new CssToInlineStyles())->convert($message['body'], $css);

        return $message;
    }

    protected function getCss(MailableInterface $mailable): array
    {
        $libraries = $mailable->getLibraries();
        $assets = (new AttachedAssets)->setLibraries($libraries);
        $cssAssets = $this->assetResolver->getCssAssets($assets, false);

        return array_filter(
            array_map(
                function (array $asset) {
                    if ($asset['external'] ?? false) {
                        return null;
                    }

                    $path = $this->fileSystem->realpath($asset['data']);

                    return file_get_contents($path);
                },
                $cssAssets
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function create(
        ContainerInterface $container,
        array $configuration,
        $plugin_id,
        $plugin_definition
    ) {
        return new static(
            $container->get('plugin.manager.mail'),
            $container->get('renderer'),
            $container->get('file_system'),
            $container->get('asset.resolver')
        );
    }
}
