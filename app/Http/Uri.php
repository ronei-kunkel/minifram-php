<?php

namespace Minifram\Http;

use Psr\Http\Message\UriInterface;

class Uri implements UriInterface
{
  private string $scheme;
  private ?int   $port;
  private string $userInfo;
  private string $host;
  private string $authority;
  private string $path;
  private string $query;
  private string $fragment;

  private const SCHEME_PORTS = [
    'http'  => 80,
    'https' => 443
  ];

  private const ALLOWED_SCHEMES = [
    '',
    'http',
    'https',
  ];

  public function __construct(string $uri)
  {
    if (!filter_var($uri, FILTER_VALIDATE_URL)) throw new \Exception("Invalid URI");

    $uri = parse_url($uri);

    $this->setScheme($uri['scheme'] ?? '');
    $this->setPort($uri['port'] ?? null);
    $this->setUserInfo($uri['user'] ?? '', $uri['pass'] ?? '');
    $this->setHost($uri['host'] ?? '');
    $this->setAuthority();
    $this->setPath($uri['path'] ?? '');
    $this->setQuery($uri['query'] ?? '');
    $this->setFragment($uri['fragment'] ?? '');
  }

  private function setScheme(string $scheme): void
  {
    $this->scheme = empty($scheme) ? '' : strtolower($scheme);
  }

  /**
   * Retrieve the scheme component of the URI.
   *
   * If no scheme is present, this method MUST return an empty string.
   *
   * The value returned MUST be normalized to lowercase, per RFC 3986
   * Section 3.1.
   *
   * The trailing ":" character is not part of the scheme and MUST NOT be
   * added.
   * @return string The URI scheme.
   */
  public function getScheme(): string
  {
    return $this->scheme;
  }

  /**
   * Set Authority of Uri
   *
   * @return void
   */
  private function setAuthority(): void
  {
    $this->authority = (empty($this->userInfo) ? '' : "$this->userInfo@") . $this->host . (is_null($this->port) ? '' : ":$this->port");
  }

  /**
   * Retrieve the authority component of the URI.
   *
   * If no authority information is present, this method MUST return an empty
   * string.
   *
   * The authority syntax of the URI is:
   *
   * <pre>
   * [user-info@]host[:port]
   * </pre>
   *
   * If the port component is not set or is the standard port for the current
   * scheme, it SHOULD NOT be included.
   * @return string The URI authority, in "[user-info@]host[:port]" format.
   */
  public function getAuthority(): string
  {
    return $this->authority;
  }

  /**
   * Set UserInfo of Uri
   *
   * @param string $user
   * @param string $pass
   * @return void
   */
  private function setUserInfo(string $user, string $pass): void
  {
    $this->userInfo = (empty($user) ? '' : "$user") . (empty($pass) ? '' : ":$pass");
  }

  /**
   * Retrieve the user information component of the URI.
   *
   * If no user information is present, this method MUST return an empty
   * string.
   *
   * If a user is present in the URI, this will return that value;
   * additionally, if the password is also present, it will be appended to the
   * user value, with a colon (":") separating the values.
   *
   * The trailing "@" character is not part of the user information and MUST
   * NOT be added.
   * @return string The URI user information, in "username[:password]" format.
   */
  public function getUserInfo(): string
  {
    return $this->userInfo;
  }

  /**
   * Set host of Uri
   *
   * @param string $host
   * @return void
   */
  private function setHost(string $host): void
  {
    $this->host = empty($host) ? '' : strtolower($host);
  }

  /**
   * Retrieve the host component of the URI.
   *
   * If no host is present, this method MUST return an empty string.
   *
   * The value returned MUST be normalized to lowercase, per RFC 3986
   * Section 3.2.2.
   * @return string The URI host.
   */
  public function getHost(): string
  {
    return $this->host;
  }

  /**
   * Set port of Uri
   *
   * @param integer|null $port
   * @return void
   */
  private function setPort(?int $port): void
  {
    if(!is_numeric($port) or is_null($port)) {
      $this->port = null;
      return;
    }

    $havePort = (is_numeric($port) and !is_null($port));

    $haveScheme = (strlen($this->scheme));

    $isSchemeStandadPort = ($haveScheme and $havePort and $port === self::SCHEME_PORTS[$this->scheme]);

    if($isSchemeStandadPort or (!$havePort and !$haveScheme) or (!$havePort and $haveScheme)) {
      $this->port = null;
      return;
    }

    $this->port = $port;
  }

  /**
   * Retrieve the port component of the URI.
   *
   * If a port is present, and it is non-standard for the current scheme,
   * this method MUST return it as an integer. If the port is the standard port
   * used with the current scheme, this method SHOULD return null.
   *
   * If no port is present, and no scheme is present, this method MUST return
   * a null value.
   *
   * If no port is present, but a scheme is present, this method MAY return
   * the standard port for that scheme, but SHOULD return null.
   * @return ?int The URI port.
   */
  public function getPort(): ?int
  {
    return $this->port;
  }

  /**
   * Set Path of Uri
   *
   * @param string $path
   * @return void
   */
  private function setPath(string $path): void
  {
    if(empty($path)) {
      $this->path = '';
      return;
    }

    $encodedPath = '';

    $explodedPath = explode('/', $path);

    foreach ($explodedPath as $keySegment => $segment) {
      $encodedPath .= rawurlencode($segment);

      if($keySegment == array_key_last($explodedPath)) continue;

      $encodedPath .= '/';
    }

    $this->path = $encodedPath;
  }
  
  /**
   * Retrieve the path component of the URI.
   *
   * The path can either be empty or absolute (starting with a slash) or
   * rootless (not starting with a slash). Implementations MUST support all
   * three syntaxes.
   *
   * Normally, the empty path "" and absolute path "/" are considered equal as
   * defined in RFC 7230 Section 2.7.3. But this method MUST NOT automatically
   * do this normalization because in contexts with a trimmed base path, e.g.
   * the front controller, this difference becomes significant. It's the task
   * of the user to handle both "" and "/".
   *
   * The value returned MUST be percent-encoded, but MUST NOT double-encode
   * any characters. To determine what characters to encode, please refer to
   * RFC 3986, Sections 2 and 3.3.
   *
   * As an example, if the value should include a slash ("/") not intended as
   * delimiter between path segments, that value MUST be passed in encoded
   * form (e.g., "%2F") to the instance.
   * @return string The URI path.
   */
  public function getPath(): string
  {
    return $this->path;
  }

  /**
   * Set Query of Uri
   *
   * @param string $query
   * @return void
   */
  private function setQuery(string $query): void
  {
    if(empty($query)) {
      $query = '';
    }

    $this->query = $query;
  }

  /**
   * Retrieve the query string of the URI.
   *
   * If no query string is present, this method MUST return an empty string.
   *
   * The leading "?" character is not part of the query and MUST NOT be
   * added.
   *
   * The value returned MUST be percent-encoded, but MUST NOT double-encode
   * any characters. To determine what characters to encode, please refer to
   * RFC 3986, Sections 2 and 3.4.
   *
   * As an example, if a value in a key/value pair of the query string should
   * include an ampersand ("&") not intended as a delimiter between values,
   * that value MUST be passed in encoded form (e.g., "%26") to the instance.
   * @return string The URI query string.
   */
  public function getQuery(): string
  {
    return $this->query;
  }

  /**
   * Set Fragment of Uri
   *
   * @param string $fragment
   * @return void
   */
  private function setFragment(string $fragment): void
  {
    $this->fragment = $fragment;
  }

  /**
   * Retrieve the fragment component of the URI.
   *
   * If no fragment is present, this method MUST return an empty string.
   *
   * The leading "#" character is not part of the fragment and MUST NOT be
   * added.
   *
   * The value returned MUST be percent-encoded, but MUST NOT double-encode
   * any characters. To determine what characters to encode, please refer to
   * RFC 3986, Sections 2 and 3.5.
   * @return string The URI fragment.
   */
  public function getFragment(): string
  {
    return $this->fragment;
  }

  /**
   * Return an instance with the specified scheme.
   *
   * This method MUST retain the state of the current instance, and return
   * an instance that contains the specified scheme.
   *
   * Implementations MUST support the schemes "http" and "https" case
   * insensitively, and MAY accommodate other schemes if required.
   *
   * An empty scheme is equivalent to removing the scheme.
   *
   * @param string $scheme The scheme to use with the new instance.
   * @return UriInterface A new instance with the specified scheme.
   */
  public function withScheme($scheme = ''): UriInterface
  {
    $scheme = strtolower($scheme);

    if(!in_array($scheme, self::ALLOWED_SCHEMES)) throw new \Exception("Unsupported scheme, must be 'http', 'https' or empty", 1);

    $uri = clone $this;

    $uri->scheme = $scheme;

    return $uri;
  }

  /**
   * Return an instance with the specified user information.
   *
   * This method MUST retain the state of the current instance, and return
   * an instance that contains the specified user information.
   *
   * Password is optional, but the user information MUST include the
   * user; an empty string for the user is equivalent to removing user
   * information.
   *
   * @param string $user The user name to use for authority.
   * @param string|null $password The password associated with $user.
   * @return UriInterface A new instance with the specified user information.
   */
  public function withUserInfo($user, $password = null): UriInterface
  {
    $uri = clone $this;

    if(is_null($password)) $password = '';

    if(empty($user)) {
      $user = '';
      $password = '';
    }

    $uri->setUserInfo($user, $password);

    return $uri;
  }

  /**
   * Return an instance with the specified host.
   *
   * This method MUST retain the state of the current instance, and return
   * an instance that contains the specified host.
   *
   * An empty host value is equivalent to removing the host.
   *
   * @param string $host The hostname to use with the new instance.
   * @return UriInterface A new instance with the specified host.
   */
  public function withHost($host): UriInterface
  {
    $uri = clone $this;

    if(empty($host)) $host = '';

    $uri->setHost($host);

    return $uri;
  }

  /**
   * Return an instance with the specified port.
   *
   * This method MUST retain the state of the current instance, and return
   * an instance that contains the specified port.
   *
   * Implementations MUST raise an exception for ports outside the
   * established TCP and UDP port ranges.
   *
   * A null value provided for the port is equivalent to removing the port
   * information.
   *
   * @param int|null $port The port to use with the new instance; a null value
   *                       removes the port information.
   * @return UriInterface A new instance with the specified port.
   */
  public function withPort($port): UriInterface
  {
    $uri = clone $this;

    $uri->setPort($port);

    return $uri;
  }

  /**
   * Return an instance with the specified path.
   *
   * This method MUST retain the state of the current instance, and return
   * an instance that contains the specified path.
   *
   * The path can either be empty or absolute (starting with a slash) or
   * rootless (not starting with a slash). Implementations MUST support all
   * three syntaxes.
   *
   * If the path is intended to be domain-relative rather than path relative then
   * it must begin with a slash ("/"). Paths not starting with a slash ("/")
   * are assumed to be relative to some base path known to the application or
   * consumer.
   *
   * Users can provide both encoded and decoded path characters.
   * Implementations ensure the correct encoding as outlined in getPath().
   *
   * @param string $path The path to use with the new instance.
   * @return UriInterface A new instance with the specified path.
   */
  public function withPath($path): UriInterface
  {
    $uri = clone $this;

    $startWithSlash = (substr($path, 0, 1) === '/');

    if(!$startWithSlash) $path = '/'.$path;

    $uri->setPath($path);

    return $uri;
  }

  /**
   * Return an instance with the specified query string.
   *
   * This method MUST retain the state of the current instance, and return
   * an instance that contains the specified query string.
   *
   * Users can provide both encoded and decoded query characters.
   * Implementations ensure the correct encoding as outlined in getQuery().
   *
   * An empty query string value is equivalent to removing the query string.
   *
   * @param string $query The query string to use with the new instance.
   * @return UriInterface A new instance with the specified query string.
   */
  public function withQuery($query): UriInterface
  {
    $uri = clone $this;

    $uri->setQuery($query);

    return $uri;
  }

  /**
   * Return an instance with the specified URI fragment.
   *
   * This method MUST retain the state of the current instance, and return
   * an instance that contains the specified URI fragment.
   *
   * Users can provide both encoded and decoded fragment characters.
   * Implementations ensure the correct encoding as outlined in getFragment().
   *
   * An empty fragment value is equivalent to removing the fragment.
   *
   * @param string $fragment The fragment to use with the new instance.
   * @return UriInterface A new instance with the specified fragment.
   */
  public function withFragment($fragment): UriInterface
  {
    $uri = clone $this;

    $uri->setFragment($fragment);

    return $uri;
  }

  /**
   * Return the string representation as a URI reference.
   *
   * Depending on which components of the URI are present, the resulting
   * string is either a full URI or relative reference according to RFC 3986,
   * Section 4.1. The method concatenates the various components of the URI,
   * using the appropriate delimiters:
   *
   * - If a scheme is present, it MUST be suffixed by ":".
   * - If an authority is present, it MUST be prefixed by "//".
   * - The path can be concatenated without delimiters. But there are two
   * cases where the path has to be adjusted to make the URI reference
   * valid as PHP does not allow to throw an exception in __toString():
   * - If the path is rootless and an authority is present, the path MUST
   * be prefixed by "/".
   * - If the path is starting with more than one "/" and no authority is
   * present, the starting slashes MUST be reduced to one.
   * - If a query is present, it MUST be prefixed by "?".
   * - If a fragment is present, it MUST be prefixed by "#".
   * @return string
   */
  public function __toString(): string
  {
    $uri = empty($this->scheme) ? '' : $this->scheme . ':';
    $uri .= empty($this->authority) ? '' : '//' . $this->authority;
    $uri .= empty($this->path) ? '' : $this->path;
    $uri .= empty($this->query) ? '' : '?' . $this->query;
    $uri .= empty($this->fragment) ? '' : '#' . $this->fragment;

    return $uri;
  }
}