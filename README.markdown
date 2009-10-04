## Installation?

The installation requires the PEAR installer, which keeps track of the rest.

    git clone git://github.com/till/Services_Scrim.git
    cd Services_Scrim
    pear package
    pear install -f Services_Scrim-0.1.0.tgz

## Running tests?

    phpunit tests/Services_ScrimTestCase.php

## Example?

    $service = new Services_Scrim;
    $service->setEmail('your@email');
    $scrim = $service->generate();

    echo $scrim;

    var_dump($scrim->getUrl(), $scrim->getEmail(), $scrim->isOld());