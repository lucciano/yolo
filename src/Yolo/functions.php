<?php

namespace Yolo;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\TerminableInterface;
use Yolo\DependencyInjection\YoloExtension;
use Yolo\Compiler\EventSubscriberPass;

function createContainer(array $parameters = [])
{
    $container = new ContainerBuilder();
    $container->registerExtension(new YoloExtension());
    $container->loadFromExtension('yolo');

    $container->getParameterBag()->add($parameters);

    $container->addCompilerPass(new EventSubscriberPass());
    $container->compile();

    return $container;
}

function handleException(Request $request)
{
    $handler = new HttpKernel\Debug\ExceptionHandler();
    $exception = $request->get('exception');

    return $handler->createResponse($exception);
}

function run(HttpKernelInterface $kernel)
{
    $request = Request::createFromGlobals();
    $response = $kernel
        ->handle($request)
        ->prepare($request)
        ->send();

    if ($kernel instanceof TerminableInterface) {
        $kernel->terminate($request, $response);
    }
}
