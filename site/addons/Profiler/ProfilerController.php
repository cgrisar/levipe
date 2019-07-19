<?php

namespace Statamic\Addons\Profiler;

use Statamic\API\Helper;
use Statamic\API\Request;
use Statamic\API\Fieldset;
use Statamic\Data\Users\User;
use Statamic\Extend\Controller;
use Statamic\API\User as UserAPI;
use Statamic\API\Fieldset as FieldsetAPI;

class ProfilerController extends Controller
{
    /** @var User */
    private $user;

    /**
     * Update a user with new data.
     *
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function postUser($id)
    {
        $this->user = UserAPI::find($id);

        $fields = $this->getFields(Fieldset::get('user'));
        $validator = $this->runValidation($fields);

        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator, 'profiler');
        }

        $this->user
            ->data(
                array_merge(
                    $this->user->data(),
                    array_except($fields, 'username')
                )
            )->save();

        $this->flash->put('success', true);

        return Request::has('redirect') ? redirect(Request::get('redirect')) : back();
    }

    /**
     * Delete a user with new data.
     *
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function deleteUser($id)
    {
        UserAPI::find($id)->delete();

        return Request::has('redirect') ? redirect(Request::get('redirect')) : back();
    }

    /**
     * Get the Validator instance
     *
     * @return mixed
     */
    private function runValidation($data = [])
    {
        $fieldset = FieldsetAPI::get('user');

        $rules = [];
        $attributes = [];

        // make the rules
        foreach ($fieldset->fields() as $field_name => $field_config) {
            if ($field_rules = array_get($field_config, 'validate')) {
                $rules[$field_name] = $field_rules;
            }

            // Define the attribute (friendly name) so it doesn't appear as field.fieldname
            $attributes[$field_name] = array_get($field_config, 'display', $field_name);
        }

        /*
            if the fieldset has assets AND there are no assets in the fields, remove the validation
            on the assumption that if there's no file in the request, they don't want to change it

            @todo how to handle file deletions???????
         */
        if ($this->fieldsetHasAssets($fieldset) && $fileFields->count() == 0) {
            $rules = collect($rules)
                // set the file ones to null
                ->filterWithKey(function ($item, $key) use ($data) {
                    return array_has($data, $key);
                })
                ->all();
        }

        // ensure there's a username
        $rules['username'] = 'required';

        // if there's a username and it's different than the current one, ensure it's unique
        if (Request::has('username') && Request::get('username') != $this->user->username()) {
            $rules['username'] .= '|not_in:' . UserAPI::pluck('username')->implode(',');
        }

        $data['username'] = Request::get('username') ?? $this->user->username();

        // if we're resetting the password, add the validation rules and the fields
        if (Request::has('password')) {
            $rules['password'] = 'required|confirmed';

            $data += Request::only(['password', 'password_confirmation']);
        }

        return app('validator')->make($data, $rules, [], $attributes);
    }

    private function fieldsetHasAssets($fieldset)
    {
        return collect($fieldset->fields())->contains(function ($key, $field) {
            return $field['type'] == 'assets';
        });
    }

    private function getFields($fieldset)
    {
        return array_intersect_key(
            request()->all(),
            array_flip(
                array_keys(
                    array_merge(
                        $fieldset->fields(),
                        Helper::ensureArray($fieldset->taxonomies())
                    )
                )
            )
        );
    }
}
