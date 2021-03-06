<?php namespace Fully\Http\Controllers\Admin;

use Fully\Http\Controllers\Controller;
use Fully\Repositories\Project\ProjectInterface;
use Redirect;
use View;
use Input;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Notification;
use Fully\Repositories\Project\ProjectRepository as Project;
use Fully\Exceptions\Validation\ValidationException;

/**
 * Class ProjectController
 * @package App\Controllers\Admin
 * @author Sefa Karagöz
 */
class ProjectController extends Controller {

    protected $project;

    public function __construct(ProjectInterface $project) {

        $this->project = $project;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {

        $page = Input::get('page', 1);
        $perPage = 10;
        $pagiData = $this->project->paginate($page, $perPage, true);
        $projects = new LengthAwarePaginator($pagiData->items, $pagiData->totalItems, $perPage, [
            'path' => Paginator::resolveCurrentPath()
        ]);

        $projects->setPath("");

        return view('backend.project.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {

        return view('backend.project.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store() {

        try {
            $this->project->create(Input::all());
            Notification::success('Project was successfully added');
            return langRedirectRoute('admin.project.index');
        } catch (ValidationException $e) {
            return langRedirectRoute('admin.project.create')->withInput()->withErrors($e->getErrors());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id) {

        $project = $this->project->find($id);
        return view('backend.project.show', compact('project'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id) {

        $project = $this->project->find($id);
        return view('backend.project.edit', compact('project'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     * @return Response
     */
    public function update($id) {

        try {
            $this->project->update($id, Input::all());
            Notification::success('Project was successfully updated');
            return langRedirectRoute('admin.project.index');
        } catch (ValidationException $e) {

            return langRedirectRoute('admin.project.edit')->withInput()->withErrors($e->getErrors());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id) {

        $this->project->delete($id);
        Notification::success('Project was successfully deleted');
        return langRedirectRoute('admin.project.index');
    }

    /**
     * @param $id
     * @return mixed
     */
    public function confirmDestroy($id) {

        $project = $this->project->find($id);
        return view('backend.project.confirm-destroy', compact('project'));
    }
}
