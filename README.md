# Gaufrette integration tests
A small set of Gaufrette tests interacting as close to reality as possible.

[![CircleCI](https://img.shields.io/circleci/project/akovalyov/gaufrette-integration-tests.svg)]()

### Prerequsites:

* `Docker`
* `docker-compose`
* `composer`

### Installation

```bash
git clone git@github.com:akovalyov/gaufrette-integration-tests.git
composer install --prefer-source #important, because we work with dynamically applied patches
./bin/test


