<?php

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace BitBag\CmsPlugin\Twig\Extension;

use BitBag\CmsPlugin\Exception\TemplateTypeNotFound;
use BitBag\CmsPlugin\Repository\BlockRepositoryInterface;
use BitBag\CmsPlugin\Resolver\BlockResourceResolverInterface;
use BitBag\CmsPlugin\Resolver\BlockTemplateResolverInterface;

/**
 * @author Mikołaj Król <mikolaj.krol@bitbag.pl>
 * @author Patryk Drapik <patryk.drapik@bitbag.pl>
 */
final class RenderBlockExtension extends \Twig_Extension
{
    /**
     * @var BlockRepositoryInterface
     */
    private $blockRepository;

    /**
     * @var BlockTemplateResolverInterface
     */
    private $blockTemplateResolver;

    /**
     * @var BlockResourceResolverInterface
     */
    private $blockResourceResolver;

    /**
     * @param BlockRepositoryInterface $blockRepository
     * @param BlockTemplateResolverInterface $blockTemplateResolver
     * @param BlockResourceResolverInterface $blockResourceResolver
     */
    public function __construct(
        BlockRepositoryInterface $blockRepository,
        BlockTemplateResolverInterface $blockTemplateResolver,
        BlockResourceResolverInterface $blockResourceResolver
    )
    {
        $this->blockRepository = $blockRepository;
        $this->blockTemplateResolver = $blockTemplateResolver;
        $this->blockResourceResolver = $blockResourceResolver;
    }

    /**
     * @return \Twig_SimpleFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction('bitbag_render_block', [$this, 'renderBlock'], ['needs_environment' => true, 'is_safe' => ['html']]),
        ];
    }

    /**
     * @param \Twig_Environment $twigEnvironment
     * @param string $code
     *
     * @return string
     *
     * @throws TemplateTypeNotFound
     */
    public function renderBlock(\Twig_Environment $twigEnvironment, string $code): string
    {
        $block = $this->blockResourceResolver->findOrLog($code);

        if (null !== $block) {
            $template = $this->blockTemplateResolver->resolveTemplate($block);

            return $twigEnvironment->render($template, ['block' => $block]);
        }

        return '';
    }
}
