<?php namespace Jewel\Http\Controllers;

use Jewel\Handlers\HandlerUtilities;

use Requests;
use Jewel\Http\Controllers\Controller;

use Jewel\Center;
use Jewel\Person;
use Jewel\Controllers\Response;

class CenterController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	public function showData()
	{
		$center_id = Request::get('center_id');
		return $this->showPeople($center_id);
	}

	/**
	 * Display a listing of the people in the given center
	 *
	 * @param string $center The system name of the center
	 * @return Response
	 */
	public function showPeople($center_id)
	{
		// 
		$persons = Person::whereHas('entityUser', function($q) use ($center_id) {
			$q->where('parent_entities_id', 'centers:'.$center_id);
		})
		// 
		->with('image')
		// 
		->with(['entityUser' => function($q) use ($center_id) {
			$q->where('parent_entities_id', 'centers:'.$center_id);
		}])
		->orderBy('last_name')->orderBy('first_name')
		->get();

		// Separate Data By Role
		$roles = [
			'director' => '',
			'staff' => '',
			'affiliate' => ''
		];
		
		foreach ($persons as $person) {

			// Grab Person
			$centers = collect($person->entityUser->all());

			// Check if Person is the Director
			$director = $centers->where('role_name', 'director')->first();

			// Assign Director and Run the rest of the Center Listing
			if($director){
				$role_name = $director->role_name;
				
			} else{
				$role_name = $person->entityUser->first()->role_name;

					// Assign Staff
					if($person->rank == 'staff') {
						$role_name = $person->rank;
				}
			}
			
			// Grab Profile Image
			if(!$person->image){
				$img = 'imgs/profile-default.png';
			} else {
				$img = 'uploads/imgs/'.$person->image->src;
			}

			// Interpolate & Append Markup
			if (array_key_exists($role_name, $roles)) {
				$roles[$role_name] .= "
				<div class='jewel-media'>
					<div class='jewel-media-left'>
						<img class='jewel-img' src='https://www.metalab.csun.edu/faculty/{$img}' alt='Image of {$person->display_name}'>
					</div>
					<div class='jewel-media-body'>
						<ul class='jewel'>
							<li class='jewel-faculty-name'><h3 class='jewel-display-name'>{$person->display_name}</h3></li>
							<li class='jewel-role-name'>{$person->rank}</li>
							<li class='jewel-email'><strong>Email: </strong><a href='mailto:{$person->email}'>{$person->email}</a></li>
							<li class='jewel-url'><a target='_blank' href='http://www.csun.edu/faculty/profiles/{$person->getEmailURIAttribute()}'>View Profile</a></li>
						</ul>
					</div>
				</div>
				";
			}
		}

		// Build Listing
		$entityList = "
		<style> 
			.jewel-media{
				margin: 25px 0;
			}
			.jewel-media-left{
			    display: table-cell;
    			vertical-align: middle;
			}
			.jewel-media-body{
				display: table-cell;
    			vertical-align: middle;
    			width: 500px;
			}
			.jewel-url a{
				color: #CF0A2C;
			}
			.jewel-img {
				float: left;
				max-width: 150px;
				display: block;
				vertical-align: middle;
			}
			.jewel-role-name{
				font-size: 1.15em;
			}
			.jewel-display-name{
				color: #4a4a4a;
			    font-size: 1.4em;
    			margin: 5px 0;
			}
			.jewel{
				color: #4a4a4a;
				list-style:outside none;
				clear: both;
			}
		</style> 
		";

		foreach ($roles as $role => $data) {
			if (!empty($data)) {
				$entityList .= "<h2 id='" . strtolower($role) . "'>".ucwords($role)."</h2>${data}<hr>";
			}
		}

		// remove control characters from the output
		$entityList = HandlerUtilities::removeControlCharacters($entityList);

		// send the response
		return $this->sendResponse($entityList);
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}
