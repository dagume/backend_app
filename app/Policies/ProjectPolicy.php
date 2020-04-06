<?php

namespace App\Policies;

use App\User;
use App\Project;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Repositories\MemberRepository;

class ProjectPolicy
{
    protected $memberRepo;
    use HandlesAuthorization;

    public function __construct(MemberRepository $memRepo){
        $this->memberRepo = $memRepo;
    }
    /**
     * Determine whether the user can view any projects.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //dd($user);
        $members = $this->memberRepo->get_members_for_a_contact($user->id);
        if(empty($members)){
            return False;
        }
        return True;
    }

    /**
     * Determine whether the user can view the project.
     *
     * @param  \App\User  $user
     * @param  \App\Project  $project
     * @return mixed
     */
    public function view(User $user, Project $project)
    {
        //dd($user,$project);
        $members = $this->memberRepo->get_user_has_project($user->id, $project->id);
        if(empty($members)){
            return False;
        }
        return True;
    }

    /**
     * Determine whether the user can create projects.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the project.
     *
     * @param  \App\User  $user
     * @param  \App\Project  $project
     * @return mixed
     */
    public function update(?User $user, Project $project)
    {
        //dd($user,$project);
        $members = $this->memberRepo->get_user_has_project($user->id, $project->id);
        if(empty($members)){
            return False;
        }
        return True;
    }

    /**
     * Determine whether the user can delete the project.
     *
     * @param  \App\User  $user
     * @param  \App\Project  $project
     * @return mixed
     */
    public function delete(User $user, Project $project)
    {
        //
    }

    /**
     * Determine whether the user can restore the project.
     *
     * @param  \App\User  $user
     * @param  \App\Project  $project
     * @return mixed
     */
    public function restore(User $user, Project $project)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the project.
     *
     * @param  \App\User  $user
     * @param  \App\Project  $project
     * @return mixed
     */
    public function forceDelete(User $user, Project $project)
    {
        //
    }
}
