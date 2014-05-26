<?php


namespace Fenchy\NoticeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Fenchy\UserBundle\Entity\User;
use Fenchy\RegularUserBundle\Entity\Document;
use Fenchy\RegularUserBundle\Entity\UserGroup;

class GlobalFilterController extends Controller
{
    public function indexV2Action($slug = null)
    {
    	$em = $this->getDoctrine()->getManager();
    	$managertype1 = array();
    	
        // Usergroup Repository
        $usergroupRepo = $this->getDoctrine()->getEntityManager()->getRepository('FenchyRegularUserBundle:UserGroup');
        // Users Repository
        $usersRepo = $this->getDoctrine()->getEntityManager()->getRepository('UserBundle:User');
        
    	$userLoggedIn = $this->get('security.context')->getToken()->getUser();
    	$usergroups = array();
    	ini_set('memory_limit', "912M");
    	$listing= array();
    	$usergroups =array();
    	$userSearchedTypes = array();
    	if($userLoggedIn instanceof User)
    	{
    		$userSearchedTypes = explode(',', $userLoggedIn->getTypesID());
    	}
    	$neighbours = array();
    	// Start Not Logged User's Search area
    	if (!($userLoggedIn instanceof User))
    	{
    		if($slug=='search')
    		{
    			$i=0;
    			$searchWord = $this->getRequest()->get('search');
    			$resultArray = $em->getRepository('FenchyNoticeBundle:Notice')->getAllUserGroups();
    			if($resultArray)
    			{
    				//$listing  = $resultArray[0];
    				//$usergroups = $resultArray[1];
    			}
    			$searchedNeighbours =  $this->searchNeighboursNotLogged($searchWord);
    			
    			$neighbours = $searchedNeighbours[0];
    			$managertype = array();
    			$i = 0;
    			if($neighbours)
    			{
    				foreach ($neighbours as $neighbour)
    				{
    					$managertype[$i] = $em
    					->getRepository('FenchyRegularUserBundle:UserRegular')
    					->getManagerType($neighbour);
    					$i++;
    				}
    			}
    			if($listing)
    			{
	    			foreach ($listing as $list)
	    			{
	    				$managertype1[$i] = $em
	    				->getRepository('FenchyRegularUserBundle:UserRegular')
	    				->getManagerType($list->getUser());
	    				$i++;
	    			}
    			}
    		}
    		
    		$listingCount = count($listing)+count($usergroups)+count($neighbours);
    		$types = $em->getRepository('FenchyNoticeBundle:Type')->getAllWithPropertiesFirst();
    		$filterService = $this->get('listfilter');
    		$emptyFilter = $returnFilter = $filterService->getFilter();
    		
    		$noticeRepo = $this->getDoctrine()->getEntityManager()->getRepository('FenchyNoticeBundle:Notice');
    		
    		$startArray =  $noticeRepo->getFirstRecord();
    		$endArray =  $noticeRepo->getLastRecord();
    		$end = '';
    		$start = strtotime($startArray[0]->getCreatedAt()->format('Y-m-d H:i:s'));
    		if($endArray && $startArray)
    		{
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
    		}
    		$starttime = time();
    		$endtime = strtotime("+1 month");
    		$daterange = $starttime.','.$endtime;
    		
    		return $this->render('FenchyNoticeBundle:GlobalFilter:indexV3.html.twig',
    				array('locale'=>$this->getRequest()->getLocale(),
    						'listingsPagination'=>$this->container->getParameter('listings_pagination'),
    						'notices' => $listing,
    						'managertype1' => $managertype1,
    						'noticeCount' => $listingCount,
    						'registered'=> false,
    						'types'=>$types,
    						'daterange' => $daterange,
    						'currdate' => $starttime,    						
    						'slider_startDate' => $start,
    						'slider_endDate' => $end,
    						'filterEmptyCat'=>$emptyFilter['categories'],
    						'filterEmptyPD'=>$emptyFilter['postDate'],
    						'filterLastUrl' => $this->get('session')->get('lastFilterUrl'),
    						'filterDistanceSliderMin'=>$this->container->getParameter('filter_min_distance'),
    						'filterDistanceSliderMax'=>$this->container->getParameter('filter_max_distance'),
    						'filterDistanceSliderDefault'=>$this->container->getParameter('distance_slider_default'),
    						'filterDistanceSliderSnap'=>$this->container->getParameter('distance_slider_snap'),
    						'filterDistanceSliderMinDate'=>$this->container->getParameter('filter_min_distanceDate'),
    						'filterDistanceSliderMaxDate'=>$this->container->getParameter('filter_max_distanceDate'),
    						'filterDistanceSliderDefaultDate'=>$this->container->getParameter('distance_slider_defaultDate'),
    						'filterDistanceSliderSnapDate'=>$this->container->getParameter('distance_slider_snapDate'),
    						'keyword' => $this->getRequest()->get('keyword'),
    						'usergroups' => $usergroups,
    						'neighbours' => $neighbours,
    						'managertype' => $managertype,
    						'listingInWideRange' => null,
    						'usergroupsInWideRange' => null,
    						'listing2WordSearch' => null,
    						'usergroups2WordSearch' => null
    						
    				));
    		//return $this->redirect($this->generateUrl('fenchy_frontend_indexv2'));
    	}
    	// End Not Logged User's Search area
    	
    	// Start Logged User's Search area
    	if($userLoggedIn instanceof User && $userLoggedIn->getLocation() == "")
    	{
    		return $this->redirect($this->generateUrl('fenchy_regular_user_user_your_location'));
    	}
    	$displayUser = $userLoggedIn;
    	$usergroups = array();
    	$origin = str_replace(" ", "", $displayUser->getLocation());
        $filterService = $this->get('listfilter');
        $emptyFilter = $returnFilter = $filterService->getFilter();
        $noticeRepo = $this->getDoctrine()->getEntityManager()->getRepository('FenchyNoticeBundle:Notice');
        
        $userLoggedIn->setIsLogin(true);
        $em->persist($userLoggedIn);
        $em->flush();
        $types = $em->getRepository('FenchyNoticeBundle:Type')->getAllWithPropertiesFirst();
        $arr_types = $this->getRequest()->get('ids');
         // added by jignesh on 9-12-2013
        if(empty($arr_types) && !empty($userSearchedTypes[0]))
        {
        	$arr_types = $userSearchedTypes; 
        }
       
        //end	
        $sortby = $this->getRequest()->get('sortby');
        $aroundyou = $this->getRequest()->get('aroundyou');
        $when = $this->getRequest()->get('when');
        $now = $this->getRequest()->get('now');
        $str = $this->getRequest()->get('keyword');
        $cookie = $this->getRequest()->get('cookies');
        $save = $this->getRequest()->get('save');
        $offergroups = $this->getRequest()->get('offergroups');
        		
        		// Searched with selected red Options 
		        if($cookie == 1 && !empty($arr_types) && $save==1)
		        {       //echo "<script>alert('bye');</script>";
		        	$typids = array();
		        	$i = 0;
		        	foreach($arr_types as $v)
		        	{
		        		$typids[] = $arr_types[$i];
		        		$i++;
		        	}
		        	$comma_typids = implode(',', $typids);
		        	
		        	$users = $this->getDoctrine()->getEntityManager()->getRepository('UserBundle:User')->findAllWithStickers($emptyFilter);
		        	foreach ($users as $user)
					{
						if ($user->getId() == $displayUser->getId())
						{
							$user->setTypesID($comma_typids);
							$em->persist($user);
							$em->flush();
						}
					}
		        	
		        }// Searched with selected all Blue Options 
		        else if ($cookie == 1 && empty($arr_types))
		        {
		        	
		        	$users = $this->getDoctrine()->getEntityManager()->getRepository('UserBundle:User')->findAllWithStickers($emptyFilter);
		        	foreach ($users as $user)
		        	{
		        		if ($user->getId() == $displayUser->getId())
		        		{
		        			$user->setTypesID(null);
		        			$em->persist($user);
		        			$em->flush();
		        		}
		        	}
		        }
		        else if($cookie == 0 && !empty($arr_types))
		        {
                                $typids = array();
		        	$i = 0;
		        	foreach($arr_types as $v)
		        	{
		        		$typids[] = $arr_types[$i];
		        		$i++;
		        	}
                                if(count($arr_types) < 13 )
		        	{ 
                                    //echo "jigs1";
                                    $comma_typids = implode(',', $typids);
                                }
                                else
                                {
                                    //echo "jigs2";
                                    $comma_typids = null;
                                }
		        	$users = $this->getDoctrine()->getEntityManager()->getRepository('UserBundle:User')->findAllWithStickers($emptyFilter);
		        	foreach ($users as $user)
		        	{
		        		if ($user->getId() == $displayUser->getId())
		        		{
		        			$user->setTypesID($comma_typids);
		        			$em->persist($user);
		        			$em->flush();
		        		}
		        	}
		        }
                        else
                        {
                            //echo "<script>alert('Hi');</script>";
                            $users = $this->getDoctrine()->getEntityManager()->getRepository('UserBundle:User')->findAllWithStickers($emptyFilter);
		        	foreach ($users as $user)
		        	{
		        		if ($user->getId() == $displayUser->getId())
		        		{
		        			$user->setTypesID(null);
		        			$em->persist($user);
		        			$em->flush();
		        		}
		        	}
                        }
		        
        if($str)
        {
        	$keyword = $this->getRequest()->get('keyword');
        }
        else
        {
        	$keyword = $this->getRequest()->get('phrs');
        }
        $searchWord ="";
        $findNeighbours ="";
        $find = $this->getRequest()->get('find');
        $findNeighbor = $this->getRequest()->get('findNeighbor');
        // Searched with keyword on top of Textbox
        if($slug=='search' && $find == null)
        {
        	$searchWord = $this->getRequest()->get('search');
        	$result2 = $em->getRepository('FenchyNoticeBundle:Notice')->getSearchResult($searchWord);
        	$listing = $result2[0];
        	
        	$location = $userLoggedIn->getLocation();
        	$lat = $location->getLatitude();
        	$log = $location->getLongitude();
        	 
        	$usergroups2 = $result2[1];
        	if($usergroups2)
        	{	
	        	foreach ($usergroups2 as $usergroup)
	        	{
	        		$location = $usergroup->getLocation();
	        		$lat2 = $location->getLatitude();
	        		$log2 = $location->getLongitude();
	        		 
	        		$theta = $log - $log2;
	        		// Find the Great Circle distance
	        		$distance = rad2deg(acos((sin(deg2rad($lat)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)))));
	        		$distance = $distance * 60 * 1.1515;
	        		$distance = round(($distance * 1609.34), 0);//miles to meter rounded to 0
	        		 
	        		if($distance < 30000 && $distance >= 0)
	        		{
	        			$usergroups[] = $usergroup;
	        			$distanceArray[] = $distance;
	        		}
	        	}
        	}
        }
        else if($slug=='search' && ($find=='neighbors' || $findNeighbor==1))
        {
        	$findNeighbours =  $this->findNeighbours($find,$aroundyou,$when,$now);
        	if($findNeighbor==1)
        	{
                    
        		$resultArray = $noticeRepo->getFullDetailedList($displayUser,$emptyFilter,$arr_types,$sortby,$aroundyou,$when,$now,$keyword,$userSearchedTypes);
        		$listing = $resultArray[0];
        		 
        		$location = $userLoggedIn->getLocation();
        		$lat = $location->getLatitude();
        		$log = $location->getLongitude();
        		if(in_array(8,$arr_types))
        		{	
        			$usergroups2 = $resultArray[1];
        		}
        		else
        		{ 
        			$usergroups2 = array(); 
        		}
        		if($usergroups2)
        		{
        			foreach ($usergroups2 as $usergroup)
        			{
        				$location = $usergroup->getLocation();
        				$lat2 = $location->getLatitude();
        				$log2 = $location->getLongitude();
        		
        				$theta = $log - $log2;
        				// Find the Great Circle distance
        				$distance = rad2deg(acos((sin(deg2rad($lat)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)))));
        				$distance = $distance * 60 * 1.1515;
        				$distance = round(($distance * 1609.34), 0);//miles to meter rounded to 0
                                            
        				$mindist2 = 0;// minimum distance
        				$maxdist2 = $this->container->getParameter('filter_max_distance_user');// maximum distance
        				 
        				if($aroundyou)
        				{
        					$dist = $aroundyou;
        				}
        				else
        				{
        					$dist = 30000; // slider distance
        				}
        				 
        				if($distance >= $mindist2 && $distance < $maxdist2 && $distance <= $dist)
        				{
        					$usergroups[] = $usergroup;
        					$distanceArray[] = $distance;
        				}
        			}
        		
        		}        		
        	}else
        	{
        		$listing = array();
        	}
        	
        }
        // Main Filtering with search tools
        else {
            
        	$resultArray = $noticeRepo->getFullDetailedList($displayUser,$emptyFilter,$arr_types,$sortby,$aroundyou,$when,$now,$keyword,$userSearchedTypes);
        	$listing = $resultArray[0];
                if($when)
                {
        	$date = explode('to',$when);
        	$startdate = date('Y-m-d H:i:s',$date[0]);
        	$enddate = date('Y-m-d H:i:s',$date[1]);
                }
                if($now)
        	{
        		$dateStart = new \DateTime();
        		$dateStart->setTime(0, 0, 1);
        		$startdate = $dateStart;
        		$dateEnd = new \DateTime();
        		$dateEnd->setTime(23, 59, 59);
        		$enddate = $dateEnd;
        		 
        	}
                
                
        	$location = $userLoggedIn->getLocation();
        	$lat = $location->getLatitude();
        	$log = $location->getLongitude();
        	if(empty($arr_types))
                {
                    $usergroups2 = $resultArray[1];
                }
                else if(in_array(8,$arr_types))
                {
                    $usergroups2 = $resultArray[1];
                }
                else
                {
                    $usergroups2 = array();
                }
                
        	if($usergroups2)
        	{	 
	        	foreach ($usergroups2 as $usergroup)
	        	{
	        		$location = $usergroup->getLocation();
	        		$lat2 = $location->getLatitude();
	        		$log2 = $location->getLongitude();
	        	
	        		$theta = $log - $log2;
	        		// Find the Great Circle distance
	        		$distance = rad2deg(acos((sin(deg2rad($lat)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)))));
	        		$distance = $distance * 60 * 1.1515;
	        		$distance = round(($distance * 1609.34), 0);//miles to meter rounded to 0
                               
                                
	        		$mindist2 = 0;// minimum distance
	        		$maxdist2 = $this->container->getParameter('filter_max_distance_user');// maximum distance
	        		
	        		if($aroundyou)
	        		{
                                  
	        			$dist = $aroundyou;
	        		}
	        		else
	        		{  
	        			$dist = 30000; // slider distance
	        		}
                                
	        		
	        			if($distance >= $mindist2 && $distance < $maxdist2 && $distance <= $dist)
	        			{
	        				$usergroups[] = $usergroup;
	        				$distanceArray[] = $distance;
                                                
	        			}
	        	}
	        	
        	}	
        	
        	
           }
        	$i = 0;
                
        	$notices = array();
        	if($listing)
        	{	
	        	foreach ($listing as $list)
	        	{
	        	 	$lat = $list->getLocation()->getLatitude();
	        		$log = $list->getLocation()->getLongitude();
	        		
	        		$lat2 = $displayUser->getLocation()->getLatitude();
	        		$log2 = $displayUser->getLocation()->getLongitude();
	        		
	        		$theta = $log - $log2;
	        		// Find the Great Circle distance
	        		$distance = rad2deg(acos((sin(deg2rad($lat)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)))));
	        		$distance = $distance * 60 * 1.1515;
	        		$gmap_distance = round(($distance * 1609.34), 0);//miles to meter rounded to 0
	        		
	        		$mindist = 0;// minimum distance
	        		$maxdist = $this->container->getParameter('filter_max_distance_user');// maximum distance
	        			        		
	        		if($aroundyou)
	        		{
	        			$dist = $aroundyou;
	        		}
	        		else
	        		{
	        			$dist = 30000; // slider distance
	        		}
	        		
	        		if($gmap_distance >= $mindist && $gmap_distance < $maxdist && $gmap_distance <= $dist)
	        		{
                                    if($when || $now)
                                    {
                                       
                                        if($list->getCreatedAt()->format('Y-m-d H:i:s') >= $startdate && $list->getCreatedAt()->format('Y-m-d H:i:s') <= $enddate && ($list->getType()!='events' && $list->getType()!='offerevents')){
                                            //echo "1";
                                            $managertype1[$i] = $em->getRepository('FenchyRegularUserBundle:UserRegular')->getManagerType($list->getUser());

                                            $i++;
                                            $notices[] = $list;
                                        }
                                        else if( $list->getStartDate()!=null && ($list->getType()=='events' || $list->getType()=='offerevents'))
                                        {
                                            //echo "2";
                                            if($list->getStartDate()->format('Y-m-d H:i:s') >= $startdate && $list->getStartDate()->format('Y-m-d H:i:s') <= $enddate)
                                            {
                                                $managertype1[$i] = $em->getRepository('FenchyRegularUserBundle:UserRegular')->getManagerType($list->getUser());

                                                $i++;
                                                $notices[] = $list;
                                            }   
                                        }
                                    }
                                    else
                                    {
                                        $managertype1[$i] = $em->getRepository('FenchyRegularUserBundle:UserRegular')->getManagerType($list->getUser());
	
	        			$i++;
	        			$notices[] = $list;
                                    }
	        		}
	        		
	        	  
	        	}
        	}
       
        
        $listing2 = $notices;
       
        $usergroupArray = $usergroupRepo->getFirstRecord();
        $usersArray = $usersRepo->getFirstRecord();
        $noticeArray = $noticeRepo->getFirstRecord();
        
        $usersTime = strtotime($usersArray[0]->getCreatedAt()->format('Y-m-d H:i:s'));
        $usergroupTime = strtotime($usergroupArray[0]->getCreatedAt()->format('Y-m-d H:i:s'));
        $noticeTime = strtotime($noticeArray[0]->getCreatedAt()->format('Y-m-d H:i:s'));
        //echo $usersTime.' = '.$usergroupTime.' = '.$noticeTime.'==>';
        $maxValue=min($usersTime,min($usergroupTime,$noticeTime));
        
        if($maxValue==$usersTime)
        {
            $startArray =  $usersArray;
        }    
        else if($maxValue==$usergroupTime)
        {
            $startArray =  $usergroupArray;
        }
        else
        {
            $startArray =  $noticeArray;
        }    
        //$startArray =  $noticeRepo->getFirstRecord();
        
    	$findLastNotices =  $noticeRepo->getLastRecord();
        $endArray = array();
        foreach ($findLastNotices as $findLastNotice)
        {
        	$lat = $findLastNotice->getLocation()->getLatitude();
        	$log = $findLastNotice->getLocation()->getLongitude();
        	
        	$lat2 = $displayUser->getLocation()->getLatitude();
        	$log2 = $displayUser->getLocation()->getLongitude();
        	
        	$theta = $log - $log2;
        	// Find the Great Circle distance
        	$distance = rad2deg(acos((sin(deg2rad($lat)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)))));
        	$distance = $distance * 60 * 1.1515;
        	$gmap_distance = round(($distance * 1609.34), 0);//miles to meter rounded to 0
        	
        	$mindist = 0;// minimum distance
        	$maxdist = $this->container->getParameter('filter_max_distance_user');// maximum distance
        	
        		if($aroundyou)
        		{
        			$dist = $aroundyou;
        		}
        		else
        		{
        			$dist = 30000; // slider distance
        		}
        		
        		if($gmap_distance >= $mindist && $gmap_distance < $maxdist && $gmap_distance <= $dist)
        		{
        			$endArray[] = $findLastNotice;
        		}
        	
        }
        if($startArray)
        {
        $start = strtotime($startArray[0]->getCreatedAt()->format('Y-m-d H:i:s'));
        }
        else
        {
            $start = null;
        }
        $end = "";
        if($endArray)
        {
            
	        if($endArray[0]->getStartDate())
	        {
	        	$end = strtotime($endArray[0]->getStartDate()->format('Y-m-d H:i:s'));
	        }
        }  
       
        $starttime = time();
        $endtime = strtotime("+1 month");
        $daterange = $starttime.','.$endtime;
        if($end=='' || $end <= $starttime)
        {
        	$end = $endtime;
        }
        
        if($findNeighbours)
        {      
        	$searchedNeighbours =  $findNeighbours;
        	//$listingCount = count($searchedNeighbours[0]);
        }
        else
        {
                $searchedNeighbours =  $this->searchNeighbours($searchWord,$aroundyou,$when,$now);
        }
        
        	
        if(empty($arr_types))
        {
        	$neighbours = $searchedNeighbours[0];
        }
        else if(in_array(1,$arr_types))
        {
                
        	$neighbours = $searchedNeighbours[0];
        }
        
        $listingCount = count($listing2)+count($usergroups)+count($neighbours);
        $managertype = array();
       	$i = 0;
       	if($neighbours)
       	{
	        foreach ($neighbours as $neighbour)
	        {
	        	$managertype[$i] = $em
		        	->getRepository('FenchyRegularUserBundle:UserRegular')
		        	->getManagerType($neighbour);        	
	        	$i++;       	
	        }
       	}
       	$listingInWideRange="";
       	$usergroupsInWideRange="";
       	if($slug == 'search' && !$listing2  && $searchWord!='')
       	{
                $result2 = $em->getRepository('FenchyNoticeBundle:Notice')->getSearchResult($searchWord);
                
                // Added by Jignesh on 19-12-2013
                $notices = $this->distanceCheckbelow30km($result2[0],$displayUser);
                // End By jignesh 
       		
                $listingInWideRange = $notices;// Changed by jignesh $result2 its before value
                
                $i=0;
       		if($listingInWideRange)
       		{
	       		foreach ($listingInWideRange as $listInWideRange)
	       		{
	       			$managertype1[$i++] = $em->getRepository('FenchyRegularUserBundle:UserRegular')->getManagerType($listInWideRange->getUser());
	       		}
       		}
                // Added by Jignesh on 19-12-2013
                $usergroups = $this->distanceCheckbelow30km($result2[1],$displayUser);
                // End By jignesh 
       		$usergroupsInWideRange = $usergroups;
       		$listingCount = count($listingInWideRange)+count($usergroupsInWideRange)+count($neighbours);
       	}
       	
       	$listing2WordSearch="";
       	$usergroups2WordSearch="";
       	if($slug == 'search' && !$listing2 && !$listingInWideRange && $searchWord!='')
       	{
       		$result2 = $em->getRepository('FenchyNoticeBundle:Notice')->getSearchResult(substr($searchWord,0,2),true);
       		
                // Added by Jignesh on 19-12-2013
                $notices = $this->distanceCheckbelow30km($result2[0],$displayUser);
                // End By jignesh 
                
                
                $listing2WordSearch = $notices; // Changed by jignesh $result2 its before value
       		$i=0;
       		if($listing2WordSearch)
       		{
	       		foreach ($listing2WordSearch as $list2WordSearch)
	       		{
	       			$managertype1[$i++] = $em->getRepository('FenchyRegularUserBundle:UserRegular')->getManagerType($list2WordSearch->getUser());
	       		}
       		}
       		// Added by Jignesh on 19-12-2013
                $usergroups = $this->distanceCheckbelow30km($result2[1],$displayUser);
                // End By jignesh 
       		$usergroups2WordSearch = $usergroups;
       		$listingCount = count($listing2WordSearch)+count($usergroups2WordSearch)+count($neighbours);
       	}
       	
       	
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
            	  'userSearchedTypes' => $userSearchedTypes,	
            	  'daterange' => $daterange,
            	  'currdate' => $starttime,
            	  'displayUser' => $displayUser,
            	  'slider_startDate' => $start,
            	  'slider_endDate' => $end,
            	  'keyword'=> $keyword,
            	  'neighbours' => $neighbours,
            	  'distance' => $searchedNeighbours[1],
            	  'managertype' => $managertype,
            	  'managertype1' => $managertype1,
            	  'registered'=> true,
            	  'findNeighbors' => $find,
            	  'usergroups' => $usergroups,	
            	  'listingInWideRange' => $listingInWideRange,
            	  'usergroupsInWideRange' => $usergroupsInWideRange,
            	  'listing2WordSearch' => $listing2WordSearch,
            	  'usergroups2WordSearch' => $usergroups2WordSearch
                ));
        // End Logged User's Search area
    }
    public function distanceCheckbelow30km($notices2,$displayUser)
    {
        $notices = array();
         if($notices2)
        	{	
	        	foreach ($notices2 as $list)
	        	{
	        	 	$lat = $list->getLocation()->getLatitude();
	        		$log = $list->getLocation()->getLongitude();
	        		
	        		$lat2 = $displayUser->getLocation()->getLatitude();
	        		$log2 = $displayUser->getLocation()->getLongitude();
	        		
	        		$theta = $log - $log2;
	        		// Find the Great Circle distance
	        		$distance = rad2deg(acos((sin(deg2rad($lat)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)))));
	        		$distance = $distance * 60 * 1.1515;
	        		$gmap_distance = round(($distance * 1609.34), 0);//miles to meter rounded to 0
	        		
                                $mindist = 0;// minimum distance
	        		$maxdist = $this->container->getParameter('filter_max_distance_user');// maximum distance
	        			        		
	        		
	        		$dist = 30000; // slider distance
	        		
	        		if($gmap_distance >= $mindist && $gmap_distance < $maxdist && $gmap_distance <= $dist)
	        		{
	        			$notices[] = $list;
	        		}
	        		
	        	  
	        	}
        	}
          return $notices;      
    }

    public function generateCodeAction($userId)
    {
    	$document = new Document();
    	$em = $this->getDoctrine()->getEntityManager();
    	$result = $em->getRepository('FenchyRegularUserBundle:Document')->findById($userId);
    	
    	if($result)
    	{
    		$avatar = $result->getWebPath();
    		if($avatar == "")
    			$avatar = 'images/default_profile_picture.png';
    	}
    	else
    	{
    		$avatar = 'images/default_profile_picture.png';
    	}
    	
    	return $this->render('FenchyNoticeBundle:PartialsV2:avatar.html.twig',
    			array('avatar'=> $avatar));
    }
    
    public function generateCodeGroupImageAction($userId)
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
    
    public function noticeDistanceCodeAction($noticeLocLat, $noticeLocLon)
    {
    	$userLoggedIn = $this->get('security.context')->getToken()->getUser();
    	
    	if ( ! $userLoggedIn instanceof \Fenchy\UserBundle\Entity\User )
    		return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_homepage'));
    	
    	$displayUser = $userLoggedIn;
    	
    	$lat = $noticeLocLat;
    	$log = $noticeLocLon;
    	
    	$lat2 = $displayUser->getLocation()->getLatitude();
    	$log2 = $displayUser->getLocation()->getLongitude();
    	
    	$theta = $log - $log2;
    	// Find the Great Circle distance
    	$distance = rad2deg(acos((sin(deg2rad($lat)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)))));
    	$distance = $distance * 60 * 1.1515;
    	$gmap_distance = round(($distance * 1609.34), 0);//miles to meter rounded to 0
    	
    	$gmap_distance_km =  $gmap_distance/1000 ; // location with gmap api distance
    	
    	$gmap_distance_km = floor($gmap_distance_km);
    	
    	return $this->render('FenchyNoticeBundle:PartialsV2:distance.html.twig',
    			array('distance'=> $gmap_distance,
    				  'distance_km' => $gmap_distance_km));
    }
    
    public function noticeDistanceClassCodeAction($noticeLocLat, $noticeLocLon)
    {
    	$userLoggedIn = $this->get('security.context')->getToken()->getUser();
    	 
    	if ( ! $userLoggedIn instanceof \Fenchy\UserBundle\Entity\User )
    		return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_homepage'));
    	 
    	$displayUser = $userLoggedIn;
    	
    	
    	$lat = $noticeLocLat;
    	$log = $noticeLocLon;
    	 
    	$lat2 = $displayUser->getLocation()->getLatitude();
    	$log2 = $displayUser->getLocation()->getLongitude();
    	 
    	$theta = $log - $log2;
    	// Find the Great Circle distance
    	$distance = rad2deg(acos((sin(deg2rad($lat)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)))));
    	$distance = $distance * 60 * 1.1515;
    	$gmap_distance = round(($distance * 1609.34), 0);//miles to meter rounded to 0
    
//     	$url = 'http://maps.googleapis.com/maps/api/distancematrix/json?origins='.$origin.'&destinations='.$destination.'&mode=driving&language=en&sensor=false';
//     	$data = file_get_contents($url);
//     	//$data = utf8_decode($data);
//     	$obj = json_decode($data);
	    	
    	
    	$gmap_distance_km =  $gmap_distance/1000 ; // location with gmap api distance
    	
    	$gmap_distance_km = floor($gmap_distance_km);
    	return $this->render('FenchyNoticeBundle:PartialsV2:distanceClass.html.twig',
    			array('distance'=> $gmap_distance,
                              'distance_km' => $gmap_distance_km));
    }
    
    public function neighborDistanceClassCodeAction($neighbourLocLat, $neighbourLocLon)
    {
    	$userLoggedIn = $this->get('security.context')->getToken()->getUser();
    
    	if ( ! $userLoggedIn instanceof \Fenchy\UserBundle\Entity\User )
    		return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_homepage'));
    
    	$displayUser = $userLoggedIn;
    	 
    	 
    	$lat = $neighbourLocLat;
    	$log = $neighbourLocLon;
    
    	$lat2 = $displayUser->getLocation()->getLatitude();
    	$log2 = $displayUser->getLocation()->getLongitude();
    
    	$theta = $log - $log2;
    	// Find the Great Circle distance
    	$distance = rad2deg(acos((sin(deg2rad($lat)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)))));
    	$distance = $distance * 60 * 1.1515;
    	$gmap_distance = round(($distance * 1609.34), 0);//miles to meter rounded to 0
    
    	//     	$url = 'http://maps.googleapis.com/maps/api/distancematrix/json?origins='.$origin.'&destinations='.$destination.'&mode=driving&language=en&sensor=false';
    	//     	$data = file_get_contents($url);
    	//     	//$data = utf8_decode($data);
    	//     	$obj = json_decode($data);
    
    	 
    	$gmap_distance_km =  $gmap_distance/1000 ; // location with gmap api distance
    	 
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
				$lat = $user->getLocation()->getLatitude();
				$log = $user->getLocation()->getLongitude();
				
				$lat2 = $displayUser->getLocation()->getLatitude();
				$log2 = $displayUser->getLocation()->getLongitude();
				
				$theta = $log - $log2;
				// Find the Great Circle distance
				$distance = rad2deg(acos((sin(deg2rad($lat)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)))));
				$distance = $distance * 60 * 1.1515;
				$gmap_distance = round(($distance * 1609.34), 0);//miles to meter rounded to 0
				
// 				$url = 'http://maps.googleapis.com/maps/api/distancematrix/json?origins='.$origin.'&destinations='.$destination.'&mode=driving&language=en&sensor=false';
// 				$data = file_get_contents($url);
// 				//$data = utf8_decode($data);
// 				$obj = json_decode($data);
	
				
				$mindist = 0;// minimum distance
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
// 				print_r($obj->error_message == 'You have exceeded your daily request quota for this API.');
// 				exit;
				
				if($gmap_distance >= $mindist && $gmap_distance < $maxdist && $gmap_distance <= $dist)
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
    			array('displayUser'		=> $displayUser,
				 	  'users2'			=> $users2));
    }
    public function myNeighborsCode1Action($userId)
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
				$lat = $user->getLocation()->getLatitude();
				$log = $user->getLocation()->getLongitude();
				
				$lat2 = $displayUser->getLocation()->getLatitude();
				$log2 = $displayUser->getLocation()->getLongitude();
				
				$theta = $log - $log2;
				// Find the Great Circle distance
				$distance = rad2deg(acos((sin(deg2rad($lat)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)))));
				$distance = $distance * 60 * 1.1515;
				$gmap_distance = round(($distance * 1609.34), 0);//miles to meter rounded to 0
				
// 				$url = 'http://maps.googleapis.com/maps/api/distancematrix/json?origins='.$origin.'&destinations='.$destination.'&mode=driving&language=en&sensor=false';
// 				$data = file_get_contents($url);
// 				//$data = utf8_decode($data);
// 				$obj = json_decode($data);
	
				
				$mindist = 0;// minimum distance
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
// 				print_r($obj->error_message == 'You have exceeded your daily request quota for this API.');
// 				exit;
				
				if($gmap_distance >= $mindist && $gmap_distance < $maxdist && $gmap_distance <= $dist)
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
    	 
    	return $this->render('FenchyNoticeBundle:PartialsV2:myneighbors1.html.twig',
    			array('displayUser'		=> $displayUser,
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
    			$lat = $user->getLocation()->getLatitude();
    			$log = $user->getLocation()->getLongitude();
    			
    			$lat2 = $displayUser->getLocation()->getLatitude();
    			$log2 = $displayUser->getLocation()->getLongitude();
    			
    			$theta = $log - $log2;
    			// Find the Great Circle distance
    			$distance = rad2deg(acos((sin(deg2rad($lat)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)))));
    			$distance = $distance * 60 * 1.1515;
    			$gmap_distance = round(($distance * 1609.34), 0);//miles to meter rounded to 0
    			
//     			$url = 'http://maps.googleapis.com/maps/api/distancematrix/json?origins='.$origin.'&destinations='.$destination.'&mode=driving&language=en&sensor=false';
//     			$data = file_get_contents($url);
//     			//$data = utf8_decode($data);
//     			$obj = json_decode($data);
    
    			$mindist = 0;// minimum distance
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
        
    				if($gmap_distance >= $mindist && $gmap_distance < $maxdist && $gmap_distance <= $dist)
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

    	
    	public function inviteneighborstolistingAction($userId, $noticeId=null)
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
    					$lat = $user->getLocation()->getLatitude();
    					$log = $user->getLocation()->getLongitude();
    					
    					$lat2 = $displayUser->getLocation()->getLatitude();
    					$log2 = $displayUser->getLocation()->getLongitude();
    					
    					$theta = $log - $log2;
    					// Find the Great Circle distance
    					$distance = rad2deg(acos((sin(deg2rad($lat)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)))));
    					$distance = $distance * 60 * 1.1515;
    					$gmap_distance = round(($distance * 1609.34), 0);//miles to meter rounded to 0
    					
//     					$url = 'http://maps.googleapis.com/maps/api/distancematrix/json?origins='.$origin.'&destinations='.$destination.'&mode=driving&language=en&sensor=false';
//     					$data = file_get_contents($url);
//     					//$data = utf8_decode($data);
//     					$obj = json_decode($data);
    	
    	
    					$mindist = 0;// minimum distance
    					$maxdist = $this->container->getParameter('filter_max_distance_user');// maximum distance
    	
    							$dist = 30000; // Default distance
    	
    							if($gmap_distance >= $mindist && $gmap_distance < $maxdist && $gmap_distance <= $dist)
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
    	
    		return $this->render('FenchyNoticeBundle:GlobalFilter:inviteneighborstolisting.html.twig',
    				array('displayUser'	=> $displayUser,
    						'users2'	=> $users2,
    						'noticeId' => $noticeId
    		));
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
    
    public function listAction() {
    
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
    
    public function searchNeighboursNotLogged($searchWord)
    {
    	
    	if($searchWord)
    	{
    		$users = $this->getDoctrine()
    		->getEntityManager()
    		->getRepository('UserBundle:User')
    		->searchUser($searchWord);
    	}
    	else
    	{
    		$filterService = $this->get('listfilter');
    		$emptyFilter = $returnFilter = $filterService->getFilter();
    	
    		$users = $this->getDoctrine()
    		->getEntityManager()
    		->getRepository('UserBundle:User')
    		->findAllWithStickers($emptyFilter);
    	}
    	 
    	
    	if($users)
    	{
    		$result[0] = $users;
    		return $result;
    	}
    	$result[0] = null;
    	return $result;
    }
    
    public function searchNeighbours($searchWord,$aroundyou,$when=null,$now=null)
    {
    	$userLoggedIn = $this->get('security.context')->getToken()->getUser();
    	
    	if ( ! $userLoggedIn instanceof \Fenchy\UserBundle\Entity\User )
    		return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_homepage'));
    	
    	$location = $userLoggedIn->getLocation();
    	$lat = $location->getLatitude();
    	$log = $location->getLongitude();    	

    	if($searchWord)
    	{
    		$users = $this->getDoctrine()
    		->getEntityManager()
    		->getRepository('UserBundle:User')
    		->searchUser($searchWord);
    	}
    	else if($when!='' || $now!='')
    	{
    		$filterService = $this->get('listfilter');
    		$emptyFilter = $returnFilter = $filterService->getFilter();
    		$users = $this->getDoctrine()
    		->getEntityManager()
    		->getRepository('UserBundle:User')
    		->findAllWithStickersWithDate($emptyFilter,$when,$now);
    	}
    	else
    	{
            
    		$filterService = $this->get('listfilter');
    		$emptyFilter = $returnFilter = $filterService->getFilter();
    		
    		$users = $this->getDoctrine()
    		->getEntityManager()
    		->getRepository('UserBundle:User')
    	   	->findAllWithStickers($emptyFilter);
    	}
    	
    	$users2 = array();
    	$distanceArray = array();
    	if($users)
    	{
           
    		foreach ($users as $user)
    		{
				$location = $user->getLocation();
				$lat2 = $location->getLatitude();
				$log2 = $location->getLongitude();		
				
				$theta = $log - $log2;
				// Find the Great Circle distance
				$distance = rad2deg(acos((sin(deg2rad($lat)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)))));
				$distance = $distance * 60 * 1.1515;
				$distance = round(($distance * 1609.34), 0);//miles to meter rounded to 0
				
				if($aroundyou)
				{
					$dist = $aroundyou;
				}
				else
				{
					$dist = 30000; // slider distance
				}
				
				if($user->getId() != $userLoggedIn->getId())
				{
					if($distance <= $dist && $distance >= 0)
					{
                                            
						$users2[] = $user;
						$distanceArray[] = $distance;
					}
				}
    		}
    		$result[0] = $users2;
    		$result[1] = $distanceArray;
    		return $result;
    	}
    		$result[0] = null;
    		$result[1] = null;
    	return $result;
    }
    
    
    public function findNeighbours($searchWord,$aroundyou=null,$when=null,$now=null)
    {
    	$userLoggedIn = $this->get('security.context')->getToken()->getUser();
    	 
    	if ( ! $userLoggedIn instanceof \Fenchy\UserBundle\Entity\User )
    		return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_homepage'));
    	 
    	$location = $userLoggedIn->getLocation();
    	$lat = $location->getLatitude();
    	$log = $location->getLongitude();
    
    	if($when!='' || $now!='')
    	{
            //echo "786";
    		$filterService = $this->get('listfilter');
    		$emptyFilter = $returnFilter = $filterService->getFilter();
    		$users = $this->getDoctrine()
    		->getEntityManager()
    		->getRepository('UserBundle:User')
    		->findAllWithStickersWithDate($emptyFilter,$when,$now);
    	}
    	else
    	{	
    	$users = $this->getDoctrine()
    	->getEntityManager()
    	->getRepository('UserBundle:User')
    	->searchUserNeighbors($searchWord);
    	}
    	 
    	$users2 = array();
    	$distanceArray = array();
    	if($users)
    	{
    		foreach ($users as $user)
    		{
    			$location = $user->getLocation();
    			$lat2 = $location->getLatitude();
    			$log2 = $location->getLongitude();
    
    			$theta = $log - $log2;
    			// Find the Great Circle distance
    			$distance = rad2deg(acos((sin(deg2rad($lat)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)))));
    			$distance = $distance * 60 * 1.1515;
    			$distance = round(($distance * 1609.34), 0);//miles to meter rounded to 0
    
    			if($user->getId() != $userLoggedIn->getId())
    			{
    				if($aroundyou)
    				{
    					$maxD = $aroundyou;
    				}
    				else 
    				{
    					$maxD = 30000;
    				}
    				if($distance < $maxD && $distance >= 0)
    				{
    					$users2[] = $user;
    					$distanceArray[] = $distance;
    				}
    			}
    		}
    		$result[0] = $users2;
    		$result[1] = $distanceArray;
    		return $result;
    	}
    	$result[0] = null;
    	$result[1] = null;
    	return $result;
    }
}
