<?php

namespace Celysium\Launcher\Controllers;

use Celysium\Base\Controller\Controller;
use Celysium\Responser\Responser;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class EnvironmentController extends Controller
{
    public function quick(): JsonResponse
    {
        return Responser::info($this->filters(config('launcher.quick')));
    }

    public function index(): JsonResponse
    {
        $variables = [];
        foreach ($_ENV as $key => $value) {
            $variables[] = [
                'key' => $key,
                'value' => $value,
            ];
        }
        return Responser::info($variables);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     * @throws Exception
     */
    public function store(Request $request): JsonResponse
    {
        $this->validate($request, [
            'variables.*.key'   => ['required', 'string'],
            'variables.*.value' => ['required', 'string'],
        ]);

        $source = file_get_contents(app()->environmentFilePath());
        foreach ($request->post('variables') as $variable) {
            $this->put($source, $variable['key'], $variable['value']);
        }
        file_put_contents(app()->environmentFilePath(), $source);

        return Responser::success();
    }

    private function filters(array $keys = []): array
    {
        $variables = [];
        foreach ($_ENV as $key => $value) {
            $group = $this->group($key);
            if ($keys && ! in_array($group, $keys)) {
                continue;
            }
            $variables[$group]['name'] = $group;
            $variables[$group]['variables'][] = [
                'key' => $key,
                'value' => $value,
            ];
        }
        return array_values($variables);
    }

    private function group(string $key): string
    {
        $prefix = $this->prefix($key);
        return $prefix ? strtolower($prefix) : 'miscellaneous';
    }

    private function prefix(string $key): string
    {
        return str_contains($key, '_') ? Str::before($key, '_') : '';
    }

    /**
     * @param string $source
     * @param string $key
     * @param string $value
     * @throws Exception
     */
    private function put(string &$source, string $key, string $value)
    {
        $key = strtoupper($key);
        if (array_key_exists($key, $_ENV)) {
            $current = $_ENV[$key];
            $source = preg_replace("/^$key=$current/m", $key . '="' . $value . '"', $source);

            if ($source === null) {
                throw new Exception("Unable to set key $key");
            }
        } else {
            $lines = explode("\n", $source);
            $prefix = $this->prefix($key);
            $exists = false;
            foreach ($lines as $lineNumber => $line) {
                if (str_contains($line, $prefix) && !str_contains($lines[$lineNumber + 1], $prefix)) {
                    array_splice($lines, $lineNumber + 1, 0, $key . '="' . $value . '"');
                    $exists = true;
                    break;
                }
            }
            if(! $exists) {
                if(trim(last($lines)) != '') {
                    $lines[] = "";
                }
                $lines[] = $key . '="' . $value . '"';
            }
            $source = implode("\n", $lines);
        }
    }
}
