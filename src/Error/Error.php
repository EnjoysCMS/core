<?php


namespace EnjoysCMS\Core\Error;


use Enjoys\Config\Config;
use HttpSoft\Emitter\SapiEmitter;
use HttpSoft\Message\Response;
use Symfony\Component\Yaml\Yaml;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * Class Error
 * @package App\Modules\System\Controller
 */
final class Error
{
    /**
     * @var Environment
     */
    private Environment $twig;
    /**
     * @var SapiEmitter
     */
    private SapiEmitter $emitter;

    private array $allowedCodes = [404];
    /**
     * @var Config
     */
    private Config $cnf;


    public function __construct()
    {
        $this->cnf = new Config();
        $this->cnf->addConfig($_ENV['PROJECT_DIR']  . '/config/config.yml', ['flags' => Yaml::PARSE_CONSTANT], Config::YAML);
        $this->twig = $this->getTwig();
        $this->emitter = new SapiEmitter();
    }

    public function http(int $code, string $message = null)
    {
        $template = 'error.twig';

        if($this->twig->getLoader()->exists(sprintf('%s.twig', $code))){
            $template = sprintf('%s.twig', $code);
        }

        $response = new Response($code);
        $response->getBody()->write(
            $this->twig->render(
                $template,
                [
                    'StatusCode' => $response->getStatusCode(),
                    'ReasonPhrase' => $response->getReasonPhrase(),
                    'message' => $message
                ]
            )
        );
        $this->emitter->emit($response);
        exit;
    }

    private function getTwig(): Environment
    {

        $twig_config = $this->cnf->getConfig('twig');
        $loader = new FilesystemLoader('/', $_ENV['PROJECT_DIR'] . $twig_config['template_dir'].'/errors');

        $twig = new Environment(
            $loader,
            [
                'debug' => $twig_config['debug'],
                'cache' => $_ENV['TEMP_DIR'] . $twig_config['cache_dir'],
                'auto_reload' => $twig_config['auto_reload'],
                'strict_variables' => $twig_config['strict_variables'],
                'auto_escape' => $twig_config['auto_escape'],
                'optimizations' => $twig_config['optimizations'],
                'charset' => $twig_config['charset'],
            ]
        );
        return $twig;
    }


}

