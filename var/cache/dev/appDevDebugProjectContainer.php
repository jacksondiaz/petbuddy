<?php

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.

if (\class_exists(\ContainerIplno5a\appDevDebugProjectContainer::class, false)) {
    // no-op
} elseif (!include __DIR__.'/ContainerIplno5a/appDevDebugProjectContainer.php') {
    touch(__DIR__.'/ContainerIplno5a.legacy');

    return;
}

if (!\class_exists(appDevDebugProjectContainer::class, false)) {
    \class_alias(\ContainerIplno5a\appDevDebugProjectContainer::class, appDevDebugProjectContainer::class, false);
}

return new \ContainerIplno5a\appDevDebugProjectContainer(array(
    'container.build_hash' => 'Iplno5a',
    'container.build_id' => 'c72cf4c9',
    'container.build_time' => 1525059402,
), __DIR__.\DIRECTORY_SEPARATOR.'ContainerIplno5a');