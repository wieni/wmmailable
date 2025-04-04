<?php

namespace Drupal\wmmailable\Plugin\Mail;

use Drupal\Core\Asset\AssetResolverInterface;
use Drupal\Core\Asset\AttachedAssets;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\wmmailable\MailableInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

/**
 * Provides a plugin to format mails
 *
 * @Mail(
 *     id = "mailable_inline_style",
 *     label = @Translation("Mailable - With inline styles"),
 *     description = @Translation("Mail formatter which converts attached css to inline styles.")
 * )
 */
class InlineStyleMailableFormatter extends MailableFormatterBase
{
    /** @var AssetResolverInterface */
    protected $assetResolver;
    /** @var FileSystemInterface */
    protected $fileSystem;

    public function __construct(
        MailManagerInterface $mailManager,
        AssetResolverInterface $assetResolver,
        FileSystemInterface $fileSystem
    ) {
        parent::__construct($mailManager);
        $this->assetResolver = $assetResolver;
        $this->fileSystem = $fileSystem;
    }

    public static function create(
        ContainerInterface $container,
        array $configuration,
        $plugin_id,
        $plugin_definition
    ) {
        return new static(
            $container->get('plugin.manager.mail'),
            $container->get('asset.resolver'),
            $container->get('file_system')
        );
    }

    public function format(array $message)
    {
        /** @var PlainMailableFormatter $plainFormatter */
        $plainFormatter = $this->mailManager->createInstance('mailable_plain');
        /** @var MailableInterface $mailable */
        $mailable = $message['mailable'] ?? null;

        $css = null;
        if ($mailable instanceof MailableInterface) {
            $css = implode(PHP_EOL, $this->getCss($mailable));
        }

        $message = $plainFormatter->format($message);
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
}
