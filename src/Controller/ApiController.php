<?php

namespace App\Controller;

use App\Repository\PrinterRepository;
use App\Service\SNMPHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ApiController
 * @package App\Controller
 *
 * @Route("/api")
 */
class ApiController extends AbstractController
{
    /**
     * @Route("/request-single-value/{value}/{id}", name="api_request_single_value")
     */
    public function requestSingleValue(Request $request, SNMPHelper $SNMPHelper, PrinterRepository $printerRepository)
    {
        try{
            $printer = $printerRepository->findOneBy(['id'=>$request->get('id')]);
            if(!is_null($printer)) {
                $parameter = $this->getParameter("snmp.default");
                if(key_exists($request->get("value"), $parameter)) {
                    $val = $SNMPHelper->getSingleValue($printer->getIp(), $parameter[$request->get("value")]['oid']);

                    return $this->json(['status'=>200, "result"=>$val]);
                } else {
                    return $this->json(['status'=>500, "errorMessage"=>"value unknown!"]);
                }
            } else {
                return $this->json(['status'=>500, "errorMessage"=>"printer unknown!"]);
            }
        } catch (\Exception $e) {
            return $this->json(['status'=>500, "errorMessage"=>$e->getMessage()]);
        }

    }
}
