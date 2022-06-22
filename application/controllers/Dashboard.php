<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @property LecturerModel $lecturer
 * @property StudentModel $student
 * @property LessonModel $lesson
 * @property BlogModel $blog
 * @property AgendaModel $agenda
 * Class Dashboard
 */
class Dashboard extends App_Controller
{
	/**
	 * Dashboard constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('LecturerModel', 'lecturer');
		$this->load->model('StudentModel', 'student');
		$this->load->model('BlogModel', 'blog');
		$this->load->model('AgendaModel', 'agenda');
	}

	/**
	 * Show dashboard page.
	 */
	public function index()
	{
		$data = [
			'totalLecturer' => $this->lecturer->getBy([], 'COUNT'),
			'totalStudent' => $this->student->getBy([], 'COUNT'),
			'totalBlog' => $this->blog->getBy([], 'COUNT'),
			'totalAgenda' => $this->agenda->getBy([], 'COUNT'),
		];

		$data['latestExams'] =  [];
		$data['activeTrainings'] = [];

		$this->render('dashboard/index', $data);
	}
}
