<?php

use Minifram\Http\Uri;

it('create new instance when using withScheme and not modify state of original Uri', function() {
  $uri = new Uri("http://www.domain.com.br/path?query=string#fragment");

  $newUri = $uri->withScheme('https');

  expect($uri->getScheme())->toBe('http');
  expect($newUri->getScheme())->toBe('https');
});

it('create new instance when using withUserInfo and not modify state of original Uri', function() {
  $uri = new Uri("http://www.domain.com.br/path?query=string#fragment");

  $newUri = $uri->withUserInfo('user', 'password1234');

  expect($uri->getUserInfo())->toBe('');
  expect($newUri->getUserInfo())->toBe('user:password1234');
});

it('create new instance when using withHost and not modify state of original Uri', function() {
  $uri = new Uri("http://www.domain.com.br/path?query=string#fragment");

  $newUri = $uri->withHost('www.otherdomain.com');

  expect($uri->getHost())->toBe('www.domain.com.br');
  expect($newUri->getHost())->toBe('www.otherdomain.com');
});

it('create new instance when using withPort and not modify state of original Uri', function() {
  $uri = new Uri("http://www.domain.com.br/path?query=string#fragment");

  $newUri = $uri->withPort(8080);

  expect($uri->getPort())->toBeNull();
  expect($newUri->getPort())->toBe(8080);
});

it('create new instance when using withPath and not modify state of original Uri', function() {
  $uri = new Uri("http://www.domain.com.br/path?query=string#fragment");

  $newUri = $uri->withPath('/opa/sopa/meeeeh');

  expect($uri->getPath())->toBe('/path');
  expect($newUri->getPath())->toBe('/opa/sopa/meeeeh');
});

it('create new instance when using withQuery and not modify state of original Uri', function() {
  $uri = new Uri("http://www.domain.com.br/path?query=string#fragment");

  $newUri = $uri->withQuery('otherQuery=tr&asa=be');

  expect($uri->getQuery())->toBe('query=string');
  expect($newUri->getQuery())->toBe('otherQuery=tr&asa=be');
});

it('create new instance when using withFragment and not modify state of original Uri', function() {
  $uri = new Uri("http://www.domain.com.br/path?query=string#fragment");

  $newUri = $uri->withFragment('ops');

  expect($uri->getFragment())->toBe('fragment');
  expect($newUri->getFragment())->toBe('ops');
});