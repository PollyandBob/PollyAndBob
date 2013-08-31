<?php


namespace Fenchy\NoticeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Fenchy\UserBundle\Entity\User;

class GlobalFilterController extends Controller
{
    public function indexV2Action()
    {
        $filterService = $this->get('listfilter');
        $emptyFilter = $returnFilter = $filterService->getFilter();
        return $this->render('FenchyNoticeBundle:GlobalFilter:indexV2.html.twig',
            array('locale'=>$this->getRequest()->getLocale(),
                  'listingsPagination'=>$this->container->getParameter('listings_pagination'),
                  'filterEmptyCat'=>$emptyFilter['categories'],
                  'filterEmptyPD'=>$emptyFilter['postDate'],
                  'filterDistanceSliderMin'=>$this->container->getParameter('filter_min_distance'),
                  'filterDistanceSliderMax'=>$this->container->getParameter('filter_max_distance'),
                  'filterDistanceSliderDefault'=>$this->container->getParameter('distance_slider_default'),
                  'filterDistanceSliderSnap'=>$this->container->getParameter('distance_slider_snap'),
                  'filterLastUrl' => $this->get('session')->get('lastFilterUrl')
                ));
    }

    public function detailsV2Action()
    { 
        return $this->render('FenchyNoticeBundle:GlobalFilter:detailsV2.html.twig',
            array('locale'=>$this->getRequest()->getLocale()));
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
}
