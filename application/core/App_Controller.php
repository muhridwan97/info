<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class App_Controller extends CI_Controller
{
    protected $layout = 'layouts/app';

    protected $strictRequest = true;

    private $filterMethods = [
        'index' => 'GET|HEAD|POST|PUT|PATCH', // just in case index is used as same for data and update
        'view' => 'GET|HEAD',
        'create' => 'GET',
        'save' => 'POST',
        'store' => 'POST', // same as save
        'edit' => 'GET',
        'update' => 'POST|PUT|PATCH', // allow POST for update in case we too lazy add _method input.
        'delete' => 'POST|DELETE', // allow POST for delete in case we too lazy add _method input.
        'destroy' => 'POST|DELETE', // same as delete
    ];

    /**
     * App_Controller constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Add or replace filter methods.
     *
     * @param $rules
     * @param bool $replace
     */
    public function setFilterMethods($rules, $replace = false)
    {
        $this->filterMethods = $replace ? $rules : array_merge($this->filterMethods, $rules);
    }

    /**
     * Get filter method data.
     *
     * @return array
     */
    public function getFilterMethods()
    {
        return $this->filterMethods;
    }

    /**
     * Filter request method by default.
     * By default this method is automatically invoked in hooks/middleware/RequestFilter.php
     *
     * @throws Exception
     */
    public function filterRequest()
    {
        if ($this->strictRequest) {
            $method = $this->input->post('_method');
            if (empty($method)) {
                $method = $this->input->server('REQUEST_METHOD');
            }

            $controllerMethod = $this->router->method;

            if (key_exists($controllerMethod, $this->filterMethods)) {
                $ruleMethods = $this->filterMethods[$controllerMethod];
                $allowedMethods = explode('|', $ruleMethods);

                $isAllowed = false;
                foreach ($allowedMethods as $allowedMethod) {
                    if (strtolower($method) == strtolower($allowedMethod)) {
                        $isAllowed = true;
                    }
                }
                if (!$isAllowed) {
                    throw new Exception(
                        "Method [{$method}] is not allowed, only [{$ruleMethods}]." . (is_cli() ? "\n\r" : '<br>') .
                        "If you like, you could turn off strict request mode in controller by override \$strictRequest to [false] in the controller."
                    );
                }
            } else {
                throw new Exception('Method is not defined in request filters.');
            }
        }
        // non strict mode allow to not set methods except the default.
        // unregistered method will be always passed to controller method.
    }

    /**
     * Render layout and page data.
     *
     * @param $page
     * @param array $data
     * @param string $title
     */
    protected function render($page, $data = [], $title = '')
    {
        // title is allowed to be passed in $data variable, if third param is empty use it instead.
        if (key_exists('title', $data) && empty($title)) {
            $title = $data['title'];
        }

        // build default title by method and controller if not set variable above is empty
        if(!key_exists('title', $data) && empty($title)) {
            $controller = str_replace(['_', '-'], ' ', $this->router->class);
            $method = str_replace(['_', '-'], ' ', $this->router->method);
            $title = ucwords(($method == 'index' ? '' : $method . ' ') . $controller);
        }

        $this->load->view($this->layout, compact('page', 'data', 'title'));
    }

	/**
	 * Render plain view.
	 *
	 * @param $view
	 * @param array $data
	 * @param bool $return
	 * @return object|string
	 */
	protected function renderView($view, $data = [], $return = false)
	{
		if ($return) {
			return $this->load->view($view, $data, $return);
		}
		$this->load->view($view, $data);
	}

    /**
     * Render data as json.
     *
     * @param $data
     */
    protected function renderJson($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
    }

	/**
	 * Check if request accept json.
	 *
	 * @return bool
	 */
	protected function acceptJson()
	{
		$accept = $this->input->get_request_header('accept');

		return !empty($accept) && trim(strtolower($accept)) == 'application/json';
	}

	/**
	 * Check authorization of user against model.
	 *
	 * @param $entity
	 * @param null $permission
	 * @return void
	 */
	protected function authorize($entity, $permission = null)
	{
		return AuthorizationModel::mustAuthorized($permission);
	}

    /**
     * Return validation rules.
     *
     * @return array
     */
    protected function _validation_rules()
    {
        return [];
    }

    /**
     * Registering validation rule with default title by input name.
     *
     * @param array $rules
     * @return bool
     */
    protected function validate($rules = [])
    {
        $rules = empty($rules) ? $this->_validation_rules() : $rules;
        foreach ($rules as $field => $rule) {
            $title = str_replace('[]', '', $field);
            $title = ucfirst(str_replace(['_', '-'], ' ', $title));
            $this->form_validation->set_rules($field, $title, $rule);
        }

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata([
                'status' => 'warning', 'message' => 'Form inputs is invalid' . validation_errors(),
            ]);
            return false;
        }
        return true;
    }

    /**
     * Render data as json.
     *
     * @param $data
     */
    protected function render_json($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
    }

}
