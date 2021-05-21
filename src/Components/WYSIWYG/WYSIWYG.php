<?php


namespace EnjoysCMS\Core\Components\WYSIWYG;


use DI\FactoryInterface;
use Exception;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

class WYSIWYG
{


    /**
     * @var Environment
     */
    private Environment $twig;
    /**
     * @var WysiwygInterface
     */
    private WysiwygInterface $editor;

    public function __construct(WysiwygInterface $editor, Environment $twig)
    {
        $this->twig = $twig;
        $this->editor = $editor;
    }

    static function getInstance($editorName, ContainerInterface $container): WYSIWYG
    {
        $twig = $container->get(Environment::class);
        try {
            $wysiwyg = new self($container->get($editorName), $twig);
        } catch (\Error | \DI\NotFoundException $error) {
            $wysiwyg = new self(new NullEditor(), $twig);
            $container->get(LoggerInterface::class)->withName('WYSIWYG')->error($error->getMessage());
        }
        return $wysiwyg;
    }

    /**
     * @param string $selector
     * @param string $editor
     * @param string $mode
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
    public function selector(string $selector)
    {
        $twigTemplate = $this->editor->getTwigTemplate();
        if (!$this->twig->getLoader()->exists($twigTemplate)) {
            throw new Exception(sprintf("Нет шаблона в по указанному пути: %s", $twigTemplate));
        }
        return $this->twig->render(
            $twigTemplate,
            [
                'editor' => $this->editor,
                'selector' => $selector
            ]
        );
    }

    /**
     * @param Environment $twig
     */
    public function setTwig(Environment $twig): void
    {
        $this->twig = $twig;
    }
}
