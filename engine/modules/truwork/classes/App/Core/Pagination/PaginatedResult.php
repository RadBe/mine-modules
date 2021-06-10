<?php


namespace App\Core\Pagination;


use App\Core\Database\QueryBuilder;
use App\Core\Http\Request;
use App\Core\Support\URL;
use ArrayAccess;
use Countable;
use IteratorAggregate;

class PaginatedResult implements ArrayAccess, Countable, IteratorAggregate
{
    public const TEMPLATES_DIR = __DIR__ . '/templates';

    /**
     * @var string
     */
    public static $template = self::TEMPLATES_DIR . '/default.php';

    /**
     * @var int
     */
    private $perPage;

    /**
     * @var int
     */
    private $total;

    /**
     * @var int
     */
    private $currentPage;

    /**
     * @var int
     */
    private $countPages;

    /**
     * @var array
     */
    private $result;

    /**
     * @var array|null
     */
    private $urlData;

    /**
     * @var string
     */
    private $uri;

    /**
     * PaginatedResult constructor.
     *
     * @param QueryBuilder|null $query
     * @param int $total
     * @param int $perPage
     * @param int $currentPage
     */
    public function __construct(?QueryBuilder $query, int $total, int $perPage, int $currentPage)
    {
        $this->total = $total;

        $this->countPages = ceil($total / $perPage);

        if($currentPage > $this->countPages) {
            $currentPage = $this->countPages;
        }

        if (!is_null($query)) {
            $query->limit($perPage, ($currentPage - 1) * $perPage);
        }

        $this->perPage = $perPage;
        $this->currentPage = $currentPage;
    }

    /**
     * @return bool
     */
    private function hasUrlData(): bool
    {
        return is_array($this->urlData);
    }

    /**
     * @param string $uri
     * @return string
     */
    private function clearPageFromUri(string $uri): string
    {
        return preg_replace('/(\?|\&)' . Request::$PAGE_KEY . '=([0-9]+)?/i', '', $uri);
    }

    /**
     * @return void
     */
    protected function initUri(): void
    {
        if ($this->hasUrlData()) {
            $this->uri = URL::create(
                $this->urlData['module'],
                $this->urlData['controller'],
                $this->urlData['action']
                ) . '&' . Request::$PAGE_KEY . '=';
        } else {
            $uri = $_SERVER['REQUEST_URI'] ?? '';
            if (empty($uri)) {
                $uri = '/?';
            } else {
                if (strpos($uri, '?') !== false) {
                    if (preg_match('/(\?|\&)' . Request::$PAGE_KEY . '=([0-9]+)/i', $uri, $matches)) {
                        $uri = $this->clearPageFromUri($uri) . $matches[1];
                    } else {
                        $uri .= '&';
                    }
                } else {
                    $uri .= '?';
                }
            }

            $this->uri = $uri . Request::$PAGE_KEY . '=';
        }
    }

    /**
     * @param int $page
     * @return string
     */
    private function createUrl(int $page): string
    {
        if ($page <= 1) {
            return $this->clearPageFromUri($this->uri);
        }

        return $this->uri . $page;
    }

    /**
     * @return int
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * @return int
     */
    public function getCountPages(): int
    {
        return $this->countPages;
    }

    /**
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * @return int
     */
    public function getPerPage(): int
    {
        return $this->perPage;
    }

    /**
     * @param array $result
     * @return $this
     */
    public function setResult(array $result): self
    {
        $this->result = $result;

        return $this;
    }

    /**
     * @return array
     */
    public function getResult(): array
    {
        return $this->result;
    }

    /**
     * @param string $module
     * @param string $controller
     * @param string $action
     * @return $this
     */
    public function setUrlData(string $module, string $controller, string $action): self
    {
        $this->urlData = compact('module', 'controller', 'action');

        return $this;
    }

    /**
     * @return bool
     */
    public function renderable(): bool
    {
        return $this->total > $this->perPage;
    }

    /**
     * @return string
     */
    public function render(): string
    {
        if (!$this->renderable()) {
            return '';
        }

        $this->initUri();
        return require static::$template;
    }

    /**
     * @return array
     */
    public function paginationData(): array
    {
        return [
            'page' => $this->currentPage,
            'per_page' => $this->perPage,
            'pages' => $this->countPages,
            'total' => $this->total
        ];
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset)
    {
        return isset($this->result[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        return $this->result[$offset];
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        $this->result[$offset] = $value;
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        unset($this->result[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function count()
    {
        return count($this->result);
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->result);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'result' => $this->getResult(),
            'pagination' => $this->paginationData()
        ];
    }

    /**
     * @return PaginatedResult
     */
    public static function createEmpty(): self
    {
        $result = new self(null, 0, 1, 1);
        $result->setResult([]);
        return $result;
    }
}
