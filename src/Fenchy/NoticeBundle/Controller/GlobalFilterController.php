<?php


namespace Fenchy\NoticeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Fenchy\UserBundle\Entity\User;
use Fenchy\RegularUserBundle\Entity\Document;
use Fenchy\RegularUserBundle\Entity\UserGroup;

class GlobalFilterController extends Controller
{
    public function indexV2Action()
    {
    	$userLoggedIn = $this->get('security.context')->getToken()->getUser();
    	
    	$displayUser = $userLoggedIn;
    	$origin = str_replace(" ", "", $displayUser->getLocation());
        $filterService = $this->get('listfilter');
        $emptyFilter = $returnFilter = $filterService->getFilter();
        $noticeRepo = $this->getDoctrine()->getEntityManager()->getRepository('FenchyNoticeBundle:Notice');
        
        $em = $this->getDoctrine()->getManager();
        
        $types = $em->getRepository('FenchyNoticeBundle:Type')->getAllWithPropertiesFirst();
        $arr_types = $this->getRequest()->get('ids');
        $sortby = $this->getRequest()->get('sortby');
        $aroundyou = $this->getRequest()->get('aroundyou');
        $when = $this->getRequest()->get('when');
        $now = $this->getRequest()->get('now');
        $str = $this->getRequest()->get('keyword');
        
        if($str)
        {
        	$keyword = $this->getRequest()->get('keyword');
        }
        else
        {
        	$keyword = $this->getRequest()->get('phrs');
        }
        $listing = $noticeRepo->getFullDetailedList($emptyFilter,$arr_types,$sortby,$aroundyou,$when,$now,$keyword);
        
        if($aroundyou)
        {	
        	$notices = array();
        	foreach ($listing as $list)
        	{
        		$destination = str_replace(" ", "", $list->getLocation());
        		
        		$url = 'http://maps.googleapis.com/maps/api/distancematrix/json?origins='.$origin.'&destinations='.$destination.'&mode=driving&language=en&sensor=false';
        		$data = file_get_contents($url);
        		//$data = utf8_decode($data);
        		$obj = json_decode($data);
        		
        		//echo($obj->rows[0]->elements[0]->distance->text); //km
        		//echo($obj->rows[0]->elements[0]->distance->value); // meters
        		$mindist = $this->container->getParameter('filter_min_distance_user');// minimum distance
        		$maxdist = $this->container->getParameter('filter_max_distance_user');// maximum distance
        		
        		
        		if($aroundyou)
        		{
        			$dist = $aroundyou;
        		}
        		else
        		{
        			$dist = 30000; // slider distance
        		}
        		
        		$gmap_distance =  $obj->rows[0]->elements[0]->distance->value; // location with gmap api distance
        		
        		if($gmap_distance > $mindist && $gmap_distance < $maxdist && $gmap_distance <= $dist)
        		{
        			$notices[] = $list;
        		}
        		
        	}
        }
        
        if($aroundyou)
        {
        	$listing2 = $notices;
        }
        else
        {
        	$listing2 = $listing;
        }
        
        $startArray =  $noticeRepo->getFirstRecord();
        $endArray =  $noticeRepo->getLastRecord();
        
        $start = strtotime($startArray[0]->getCreatedAt()->format('Y-m-d H:i:s'));
        if($endArray[0]->getStartDate())
        {
        	$end = strtotime($endArray[0]->getStartDate()->format('Y-m-d H:i:s'));
        }
        else
        {
        	if($endArray[1]->getStartDate())
        	{
        		$end = strtotime($endArray[1]->getStartDate()->format('Y-m-d H:i:s'));
        	}
        	else
        	{
        		$end = strtotime($endArray[1]->getCreatedAt()->format('Y-m-d H:i:s'));
        	}
        	
        }
        $listingCount = count($listing2);
        $starttime = time();
        $endtime = strtotime("+1 month");
        $daterange = $starttime.','.$endtime;
       
        return $this->render('FenchyNoticeBundle:GlobalFilter:indexV2.html.twig',
            array('locale'=>$this->getRequest()->getLocale(),
                  'listingsPagination'=>$this->container->getParameter('listings_pagination'),
                  'filterEmptyCat'=>$emptyFilter['categories'],
                  'filterEmptyPD'=>$emptyFilter['postDate'],
                  'filterDistanceSliderMin'=>$this->container->getParameter('filter_min_distance'),
                  'filterDistanceSliderMax'=>$this->container->getParameter('filter_max_distance'),
                  'filterDistanceSliderDefault'=>$this->container->getParameter('distance_slider_default'),
                  'filterDistanceSliderSnap'=>$this->container->getParameter('distance_slider_snap'),
            	  'filterDistanceSliderMinDate'=>$this->container->getParameter('filter_min_distanceDate'),
                  'filterDistanceSliderMaxDate'=>$this->container->getParameter('filter_max_distanceDate'),
                  'filterDistanceSliderDefaultDate'=>$this->container->getParameter('distance_slider_defaultDate'),
                  'filterDistanceSliderSnapDate'=>$this->container->getParameter('distance_slider_snapDate'),
            	  'filterLastUrl' => $this->get('session')->get('lastFilterUrl'),
            	  'notices' => $listing2,
            	  'noticeCount' => $listingCount,
            	  'types' => $types,
            	  'daterange' => $daterange,
            	  'currdate' => $starttime,
            	  'displayUser' => $displayUser,
            	  'slider_startDate' => $start,
            	  'slider_endDate' => $end,
            	  'keyword'=> $keyword
                ));
    }
    
    public function generateCodeAction($userId)
    {
    	$document = new Document();
    	$em = $this->getDoctrine()->getEntityManager();
    	$result = $em->getRepository('FenchyRegularUserBundle:Document')->findById($userId);
    	
    	if($result)
    	{
    		$avatar = $result->getWebPath();
    			
    	}
    	else
    	{
    		$avatar = 'images/default_profile_picture.png';
    	}
    	
    	return $this->render('FenchyNoticeBundle:PartialsV2:avatar.html.twig',
    			array('avatar'=> $avatar));
    }
    
    public function noticeDistanceCodeAction($noticeLoc)
    {
    	$userLoggedIn = $this->get('security.context')->getToken()->getUser();
    	
    	if ( ! $userLoggedIn instanceof \Fenchy\UserBundle\Entity\User )
    		return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_homepage'));
    	
    	$displayUser = $userLoggedIn;
    	$origin = str_replace(" ", "", $displayUser->getLocation());
    	$destination = str_replace(" ", "", $noticeLoc);
    	    	
    	$url = 'http://maps.googleapis.com/maps/api/distancematrix/json?origins='.$origin.'&destinations='.$destination.'&mode=driving&language=en&sensor=false';
    	$data = file_get_contents($url);
    	//$data = utf8_decode($data);
    	$obj = json_decode($data);
    	
    	$gmap_distance =  $obj->rows[0]->elements[0]->distance->value; // location with gmap api distance
    	$gmap_distance_km =  ($obj->rows[0]->elements[0]->distance->value)/1000 ; // location with gmap api distance
    	$gmap_distance_km = floor($gmap_distance_km);
    	return $this->render('FenchyNoticeBundle:PartialsV2:distance.html.twig',
    			array('distance'=> $gmap_distance,
    				  'distance_km' => $gmap_distance_km));
    }
    
    public function noticeDistanceClassCodeAction($noticeLoc)
    {
    	$userLoggedIn = $this->get('security.context')->getToken()->getUser();
    	 
    	if ( ! $userLoggedIn instanceof \Fenchy\UserBundle\Entity\User )
    		return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_homepage'));
    	 
    	$displayUser = $userLoggedIn;
    	$origin = str_replace(" ", "", $displayUser->getLocation());
    	$destination = str_replace(" ", "", $noticeLoc);
    
    	$url = 'http://maps.googleapis.com/maps/api/distancematrix/json?origins='.$origin.'&destinations='.$destination.'&mode=driving&language=en&sensor=false';
    	$data = file_get_contents($url);
    	//$data = utf8_decode($data);
    	$obj = json_decode($data);
    	 
    	$gmap_distance =  $obj->rows[0]->elements[0]->distance->value; // location with gmap api distance
    	$gmap_distance_km =  ($obj->rows[0]->elements[0]->distance->value)/1000 ; // location with gmap api distance
    	$gmap_distance_km = floor($gmap_distance_km);
    	return $this->render('FenchyNoticeBundle:PartialsV2:distanceClass.html.twig',
    			array('distance'=> $gmap_distance,
    					'distance_km' => $gmap_distance_km));
    }
    
    public function myNeighborsCodeAction($userId)
    {
    	$userLoggedIn = $this->get('security.context')->getToken()->getUser();
	
		if ( ! $userLoggedIn instanceof \Fenchy\UserBundle\Entity\User )
			return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_homepage'));
	
		$displayUser = $userLoggedIn;
		$filterService = $this->get('listfilter');
		$emptyFilter = $returnFilter = $filterService->getFilter();
		//Get Users info
		$users = $this->getDoctrine()
		->getEntityManager()
		->getRepository('UserBundle:User')
		->findAllWithStickers($emptyFilter);
	
		$origin = str_replace(" ", "", $displayUser->getLocation());
		$currentuid = $displayUser->getId();
		$request = $this->getRequest();
	
		$filterdata = "";
		$users2 = array();
		foreach ($users as $user) {
	
			//$avatar = $user->getRegularUser()->getAvatar(false);
			$em = $this->getDoctrine()->getEntityManager();
			$neighbor = $em->getRepository('FenchyRegularUserBundle:Neighbors')->findById($user->getId(),$displayUser->getId());
			if(!$neighbor)
			{
				$neighbor = $em->getRepository('FenchyRegularUserBundle:Neighbors')->findById($displayUser->getId(),$user->getId());
			}
			$neighbors = '';
			if($neighbor)
			{
				$neighbors = $neighbor->getId();
			}
		  if(!$neighbors)
		  {
			$document = new Document();
			$em = $this->getDoctrine()->getEntityManager();
			$result = $em->getRepository('FenchyRegularUserBundle:Document')->findById($user->getId());
				
			if($result)
			{
				$avatar = $result->getWebPath();
			
			}
			else
			{
				$avatar = 'images/default_profile_picture.png';
			}
			
			$firstname= $user->getRegularUser()->getFirstname();
			$destination = str_replace(" ", "", $user->getLocation());
			$userid= $user->getId();
				
				
			if($user->getLocation()!="" && $userid!=$currentuid)
			{
				$url = 'http://maps.googleapis.com/maps/api/distancematrix/json?origins='.$origin.'&destinations='.$destination.'&mode=driving&language=en&sensor=false';
				$data = file_get_contents($url);
				//$data = utf8_decode($data);
				$obj = json_decode($data);
	
				
				$mindist = $this->container->getParameter('filter_min_distance_user');// minimum distance
				$maxdist = $this->container->getParameter('filter_max_distance_user');// maximum distance
	
				// 				$dist1 = $request->getUri();
					
				// 				if(strpos($dist1,'dst=') !== false)
				// 				{
				// 					$dist1 = explode("dst=",$request->getUri());
				// 					$dist = $dist1[1]; // slider distance
				// 				}
				// 				else
				// 				{
				// 					$dist = 30000; // slider distance
				// 				}
	
				$dist = 30000; // Default distance
	
				$gmap_distance =  $obj->rows[0]->elements[0]->distance->value; // location with gmap api distance
				
				if($gmap_distance > $mindist && $gmap_distance < $maxdist && $gmap_distance <= $dist)
				{
					// Added By jignesh for Manager type
		
					if($user->getActivity() >= 400 && $user->getActivity() < 1000)
					{
						$managertype = "Community Manager";
						$managertype_alpha = "C";
						$classColor = "green";
					}
					elseif($user->getActivity() >= 1000)
					{
						$managertype = "Neighborhood Manager";
						$managertype_alpha = "N";
						$classColor = "orange";
					}
					elseif($user->getManagerType()==1)
					{
						$managertype = "House Manager";
						$managertype_alpha = "H";
						$classColor = "blue";
					}
					else
					{
						$managertype = "";
						$managertype_alpha = " ";
						$classColor = "red";
					}
					// Added By jignesh for Manager type
					$user->setTwitterUsername($avatar);
					$users2[] = $user;
	
				}
			}
		  }
		}
    	 
    	return $this->render('FenchyNoticeBundle:PartialsV2:myneighbors.html.twig',
    			array('avatar'=> $avatar,
   					  'displayUser'		=> $displayUser,
				 	  'users2'			=> $users2));
    }
    
    public function inviteNeighborsCodeAction($userId)
    {
    	$userLoggedIn = $this->get('security.context')->getToken()->getUser();
    
    	if ( ! $userLoggedIn instanceof \Fenchy\UserBundle\Entity\User )
    		return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_homepage'));
    
    	$displayUser = $userLoggedIn;
    	$filterService = $this->get('listfilter');
    	$emptyFilter = $returnFilter = $filterService->getFilter();
    	//Get Users info
    	$users = $this->getDoctrine()
    	->getEntityManager()
    	->getRepository('UserBundle:User')
    	->findAllWithStickers($emptyFilter);
    
    	$origin = str_replace(" ", "", $displayUser->getLocation());
    	$currentuid = $displayUser->getId();
    	$request = $this->getRequest();
    
    	$filterdata = "";
    	$users2 = array();
    	foreach ($users as $user) {
    
    		//$avatar = $user->getRegularUser()->getAvatar(false);
    		$em = $this->getDoctrine()->getEntityManager();
    		$neighbor = $em->getRepository('FenchyRegularUserBundle:Neighbors')->findById($currentuid,$user->getId());
    		if(!$neighbor)
    		{
    			$neighbor = $em->getRepository('FenchyRegularUserBundle:Neighbors')->findById($user->getId(),$currentuid);
    		}
    		$neighbors = '';
    		if($neighbor)
    		{
    			$neighbors = $neighbor->getId();
    		}
    	 if($neighbors)
    	 {
    		$document = new Document();
    		$em = $this->getDoctrine()->getEntityManager();
    		$result = $em->getRepository('FenchyRegularUserBundle:Document')->findById($user->getId());
    
    		if($result)
    		{
    			$avatar = $result->getWebPath();
    				
    		}
    		else
    		{
    			$avatar = 'images/default_profile_picture.png';
    		}
    			
    		$firstname= $user->getRegularUser()->getFirstname();
    		$destination = str_replace(" ", "", $user->getLocation());
    		$userid= $user->getId();
    
    
    		if($user->getLocation()!="" && $userid!=$currentuid)
    		{
    			$url = 'http://maps.googleapis.com/maps/api/distancematrix/json?origins='.$origin.'&destinations='.$destination.'&mode=driving&language=en&sensor=false';
    			$data = file_get_contents($url);
    			//$data = utf8_decode($data);
    			$obj = json_decode($data);
    
    
    			$mindist = $this->container->getParameter('filter_min_distance_user');// minimum distance
    			$maxdist = $this->container->getParameter('filter_max_distance_user');// maximum distance
    
    			// 				$dist1 = $request->getUri();
    				
    			// 				if(strpos($dist1,'dst=') !== false)
    			// 				{
    			// 					$dist1 = explode("dst=",$request->getUri());
    				// 					$dist = $dist1[1]; // slider distance
    				// 				}
    				// 				else
    				// 				{
    				// 					$dist = 30000; // slider distance
    				// 				}
    
    				$dist = 30000; // Default distance
    
    				$gmap_distance =  $obj->rows[0]->elements[0]->distance->value; // location with gmap api distance
    
    				if($gmap_distance > $mindist && $gmap_distance < $maxdist && $gmap_distance <= $dist)
    				{
    					// Added By jignesh for Manager type
    
    					if($user->getActivity() >= 400 && $user->getActivity() < 1000)
    					{
    						$managertype = "Community Manager";
    						$managertype_alpha = "C";
    						$classColor = "green";
    					}
    					elseif($user->getActivity() >= 1000)
    					{
    						$managertype = "Neighborhood Manager";
    						$managertype_alpha = "N";
    						$classColor = "orange";
    					}
    					elseif($user->getManagerType()==1)
    					{
    						$managertype = "House Manager";
    						$managertype_alpha = "H";
    						$classColor = "blue";
    					}
    					else
    					{
    						$managertype = "";
    						$managertype_alpha = " ";
    						$classColor = "red";
    					}
    					// Added By jignesh for Manager type
    					$user->setTwitterUsername($avatar);
    					$users2[] = $user;
    
    				}
    			}
    		  }
    		}
    
    		return $this->render('FenchyNoticeBundle:PartialsV2:inviteneighbors.html.twig',
    				  array('displayUser'		=> $displayUser,
    						'users2'			=> $users2));
    	}

    public function detailsV2Action()
    { 
        return $this->render('FenchyNoticeBundle:GlobalFilter:detailsV2.html.twig',
            array('locale'=>$this->getRequest()->getLocale()));
    }
    
    public function myGroupsCodeAction($userId)
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	$usergroup = $em->getRepository('FenchyRegularUserBundle:UserGroup')->findAllById($userId);
    	
    	return $this->render('FenchyNoticeBundle:PartialsV2:mygroups.html.twig',
    			array('usergroup'=> $usergroup));
    }

    public function autocompleteSuggestAction() 
    {
        $request = $this->get('request');
        if ($request->getMethod() == 'POST') {
            $dict_service = $this->get('fenchy_dictionary');
            
            $phrase = $request->get('phrase');
            $dict_entities = $dict_service->whatAutocompleteSuggestions($phrase);

            $suggestions = array();
            foreach($dict_entities as $dict_entity)
            {
                $one = array(
                    'label' => $dict_entity->getText(),
                    'value' => $dict_entity->getText(),
                    'actualValue' => $dict_entity->getId()
                );
                $suggestions[] = $one;
            }
            
            $response = new Response(json_encode($suggestions));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
            
        }
    }
    
    public function list2Action() {
		
        $request = $this->get('request');
        if ( $request->getMethod() == 'POST' ) {
            $filterService = $this->get('listfilter');
            
            $clientFilter = json_decode($request->getContent(), TRUE);
            if ( array_key_exists('toGoUrl', $clientFilter) ) {
                $this->get('session')->set('lastFilterUrl', $clientFilter['toGoUrl']);
            }
            
            $filterService->buildValidateFilter($clientFilter);
            $currentUser = $this->get('security.context')->getToken()->getUser();
            
            if ( $currentUser instanceof \Fenchy\UserBundle\Entity\User )
                $filterService->setCurrentUser($currentUser);
            
            $returnFilter = $filterService->getFilter();
            $knn = $this->container->getParameter('not_found_suggestions');
            
            $returnData = $filterService->getList();
            $returnDataCnt = $filterService->count;
            
            if ( $returnDataCnt == 0)
                $returnData = $filterService->getListKNN( $knn );

            $response = new Response(json_encode(array(
                'filter' => $returnFilter,
                'notices' => $returnData,
                'noticesCount' => $returnDataCnt
            )));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
    }
}
