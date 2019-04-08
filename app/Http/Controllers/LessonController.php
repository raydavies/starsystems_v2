<?php
namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\Level;
use App\Models\Subject;
use Illuminate\Http\Request;

class LessonController extends Controller
{
	protected $request;

	public function __construct(Request $request)
	{
		$this->request = $request;
	}

	public function index()
	{
		$level_id = (int) $this->request->input('level', 1);
		$subject_id = (int) $this->request->input('subject', 1);

		$levels = Level::all();
		$currentLevel = Level::find($level_id);
		$currentSubject = Subject::find($subject_id);
		$lessons = Lesson::whereRaw('level_id = ? AND subject_id = ?', [$level_id, $subject_id])->get();

		return view('lesson_topics', [
			'levels' => $levels,
			'subjects' => $currentLevel->subjects,
			'lessons' => $lessons,
			'current_level' => $currentLevel,
			'current_subject' => $currentSubject
		]);
	}

	public function fetchSubjects($level_id)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		return response()->json(['subjects' => Level::find($level_id)->subjects]);
	}
}
