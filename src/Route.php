<?php
/**
 * Utopia PHP Framework
 *
 * @package Framework
 * @subpackage Core
 *
 * @link https://github.com/utopia-php/framework
 * @author Appwrite Team <team@appwrite.io>
 * @license The MIT License (MIT) <http://www.opensource.org/licenses/mit-license.php>
 */

namespace Utopia;

use Exception;

class Route
{
    /**
     * HTTP Method
     *
     * @var string
     */
    protected $method = '';

    /**
     * Whether to use middleware
     *
     * @var bool
     */
    protected $middleware = true;

    /**
     * Path
     *
     * @var string
     */
    protected $path = '';

    /**
     * Alias path
     *
     * @var string
     */
    protected $aliasPath = '';

    /**
     * Alias Params
     *
     * @var array
     */
    protected $aliasParams = [];

    /**
     * Is Alias Route?
     * @var bool
     */
    protected $isAlias = false;

    /**
     * Description
     *
     * @var string
     */
    protected $desc = '';

    /**
     * Group
     *
     * @var array
     */
    protected $groups = [];

    /**
     * Action Callback
     *
     * @var callable
     */
    protected $action;

    /**
     * @var int
     */
    public static $counter = 0;

    /**
     * Parameters
     *
     * List of route params names and validators
     *
     * @var array
     */
    protected $params = [];

    /**
     * Injections
     *
     * List of route required injections for action callback
     *
     * @var array
     */
    protected $injections = [];

    /**
     * Labels
     *
     * List of route label names
     *
     * @var array
     */
    protected $labels = [];

    /**
     * @var int
     */
    protected $order;

    /**
     * @param string $method
     * @param string $path
     */
    public function __construct(string $method, string $path)
    {
        self::$counter++;

        $this->path($path);
        $this->method = $method;
        $this->order = self::$counter;
        $this->action = function (): void {};
    }

    /**
     * Add path
     *
     * @param string $path
     * @return $this
     */
    public function path($path): self
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Add alias
     *
     * @param string $path
     * @param array $params
     * @return $this
     */
    public function alias($path, $params = []): self
    {
        $this->aliasPath = $path;
        $this->aliasParams = $params;
        return $this;
    }

    /**
     * Set isAlias
     *
     * @param bool $isAlias
     *
     * @return void
     */
    public function setIsAlias($isAlias): void
    {
        $this->isAlias = $isAlias;
    }

    /**
     * Add Description
     *
     * @param string $desc
     * @return $this
     */
    public function desc($desc): self
    {
        $this->desc = $desc;
        return $this;
    }

    /**
     * Add Group
     *
     * @param array $groups
     * @return $this
     */
    public function groups(array $groups): self
    {
        $this->groups = $groups;
        return $this;
    }

    /**
     * Add Action
     *
     * @param callable $action
     * @return $this
     */
    public function action(callable $action): self
    {
        $this->action = $action;
        return $this;
    }

    /**
     * Add Param
     *
     * @param string $key
     * @param null $default
     * @param string $validator
     * @param string $description
     * @param bool $optional
     * @param array $injections
     *
     * @return $this
     */
    public function param($key, $default, $validator, $description = '', $optional = false, array $injections = []): self
    {
        $this->params[$key] = [
            'default' => $default,
            'validator' => $validator,
            'description' => $description,
            'optional' => $optional,
            'injections' => $injections,
            'value' => null,
            'order' => count($this->params) + count($this->injections),
        ];

        return $this;
    }

    /**
     * Set middleware status
     *
     * @param boolean $middleware
     *
     * @return self
     */
    public function middleware($middleware = true): self
    {
        $this->middleware = $middleware;

        return $this;
    }

    /**
     * Inject
     *
     * @param string $injection
     *
     * @return $this
     */
    public function inject($injection): self
    {
        if (array_key_exists($injection, $this->injections)) {
            throw new Exception('Injection already declared for ' . $injection);
        }

        $this->injections[$injection] = [
            'name' => $injection,
            'order' => count($this->params) + count($this->injections),
        ];

        return $this;
    }

    /**
     * Add Label
     *
     * @param string $key
     * @param mixed $value
     *
     * @return $this
     */
    public function label($key, $value): self
    {
        $this->labels[$key] = $value;
        return $this;
    }

    /**
     * Get HTTP Method
     *
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Get Alias path
     *
     * @return string
     */
    public function getAliasPath(): string
    {
        return $this->aliasPath;
    }

    /**
     * Get Alias Params
     *
     * @return array
     */
    public function getAliasParams(): array
    {
        return $this->aliasParams;
    }

    /**
     * Get is Alias
     *
     * @return bool
     */
    public function getIsAlias(): bool
    {
        return $this->isAlias;
    }

    /**
     * Get Description
     *
     * @return string
     */
    public function getDesc(): string
    {
        return $this->desc;
    }

    /**
     * Get Groups
     *
     * @return array
     */
    public function getGroups(): array
    {
        return $this->groups;
    }

    /**
     * Get Action
     *
     * @return callable
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Get Params
     *
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Get Injections
     *
     * @return array
     */
    public function getInjections(): array
    {
        return $this->injections;
    }

    /**
     * Get Param Values
     *
     * @return array
     */
    public function getParamsValues(): array
    {
        $values = [];

        foreach ($this->params as $key => $param) {
            $values[$key] = $param['value'];
        }

        return $values;
    }

    /**
     * Set Param Value
     *
     * @param string $key
     * @param mixed $value
     *
     * @return self
     *
     * @throws Exception
     */
    public function setParamValue(string $key, $value): self
    {
        if (!isset($this->params[$key])) {
            throw new Exception('Unknown key');
        }

        $this->params[$key]['value'] = $value;

        return $this;
    }

    /**
     * Get Param Value
     *
     * @param string $key
     * @return mixed
     * @throws Exception
     */
    public function getParamValue(string $key)
    {
        if (!isset($this->params[$key])) {
            throw new Exception('Unknown key');
        }

        return $this->params[$key]['value'];
    }

    /**
     * Get Label
     *
     * Return given label value or default value if label doesn't exists
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getLabel($key, $default)
    {
        return (isset($this->labels[$key])) ? $this->labels[$key] : $default;
    }

    /**
     * Get Route Order ID
     *
     * @return int
     */
    public function getOrder(): int
    {
        return $this->order;
    }

    /**
     * Get middleware status
     *
     * @return bool
     */
    public function getMiddleware(): bool
    {
        return $this->middleware;
    }
}
