<?php

namespace App\Http\Requests;


use App\Models\Course;
use App\Models\CourseInvitation;
use App\Models\User;
use Illuminate\Validation\Validator;

class StoreCourseInvitationRequest extends AuthorizedRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|exists:users,email',
            'course_id' => 'required|exists:courses,id',
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function (Validator $validator) {
            $course = Course::find($this->safe()->course_id);
            $user = User::where('email', $this->safe()->email)->first();

            if ($course->students->contains($user)) {
                $validator->errors()
                    ->add('email', 'Невозможно создать приглашение. Пользователь уже является студентом курса.');

            } else if (CourseInvitation::where(['user_id' => $user->id, 'course_id' => $course->id])->exists()) {
                $validator->errors()
                    ->add('email', 'Невозможно создать приглашение. Пользователь уже приглашён на данный курс.');
            }
        });
    }
}
