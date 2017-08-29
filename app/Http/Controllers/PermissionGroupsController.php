<?php

namespace App\Http\Controllers;

use App\Helpers\ValidationDefaults;
use App\Models\PermissionGroup;
use Illuminate\Http\Request;

class PermissionGroupsController extends Controller {

    // TODO: Check for permission on each step
    // TODO: Flush permissions cache on changes

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        /** @noinspection PhpDynamicAsStaticMethodCallInspection */
        $groups = PermissionGroup::paginate();
        return view('model.permissions.groups.index')
            ->with('groups', $groups);
    }

    /**
     * Create page.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('model.permissions.groups.create');
    }

    /**
     * Store a new permission group.
     *
     * @param \Illuminate\Http\Request $request Request.
     * @return \Illuminate\Http\Response Response.
     */
    public function store(Request $request) {
        // Validate
        $this->validate($request, [
            'name' => 'required|' . ValidationDefaults::PERMISSION_GROUP_NAME,
        ]);

        // TODO: Make sure the user has permission to do this

        // Determine whether the group is enabled
        $enabled = $request->input('enabled') == 'true';

        // Create the permission group
        $group = new PermissionGroup();
        $group->name = $request->input('name');
        $group->enabled = $enabled;
        $group->inherit_from = null;
        $group->community_id = null;
        $group->bar_id = null;
        $group->save();

        // Redirect the user to the permission group page
        // TODO: Attach dynamic language here
        return redirect()
            ->route('permissionGroups.show', ['id' => $group->id])
            ->with('success', 'Permission group created');
    }

    /**
     * Display a permission group.
     *
     * @param int $id Permission group ID.
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        /** @noinspection PhpDynamicAsStaticMethodCallInspection */
        $group = PermissionGroup::findOrFail($id);
        return view('model.permissions.groups.show')
            ->with('group', $group);
    }

    /**
     * Permission group edit page.
     *
     * @param int $id Permission group ID.
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        /** @noinspection PhpDynamicAsStaticMethodCallInspection */
        $group = PermissionGroup::findOrfail($id);
        return view('model.permissions.groups.edit')
            ->with('group', $group);
    }

    /**
     * Update a permission group edit.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        // Validate
        $this->validate($request, [
            'name' => 'required|' . ValidationDefaults::PERMISSION_GROUP_NAME,
        ]);

        // Determine whether the group is enabled
        $enabled = $request->input('enabled') == 'true';

        // Find the group
        /** @noinspection PhpDynamicAsStaticMethodCallInspection */
        /** @var PermissionGroup $group */
        $group = PermissionGroup::findOrFail($id);

        // TODO: Make sure the user has permission

        // Update the properties
        $group->name = $request->input('name');
        $group->enabled = $enabled;
        $group->save();

        // Show the group afterwards
        // TODO: Link dynamic language here
        return redirect()
            ->route('permissionGroups.show', ['id' => $id])
            ->with('success', 'Group updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        /** @noinspection PhpDynamicAsStaticMethodCallInspection */
        $group = PermissionGroup::findOrFail($id);

        // Delete the group
        $group->delete();

        // Redirect to the post index
        // TODO: Use dynamic language here
        return redirect()->route('permissionGroups.index')->with('success', 'Permission group deleted');
    }
}
