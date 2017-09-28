<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class AdminController extends Controller
{
    /**
     * Checks if user allowed to do things
     */
    private function checkUserAuth(){
        //Check if user authenticated
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        //Check user's role
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
    }

    /**
     * @Route("/reports/list", name="reports_list")
     * @Method("GET")
     */
    public function reportsAction(Request $request)
    {
        $this->checkUserAuth();

        $em = $this->getDoctrine()->getManager();

        $users = $em
            ->getRepository('AppBundle:User')
            ->createQueryBuilder('u')
            ->where('u.roles LIKE :roles')
            ->setParameter('roles', '%ROLE_ADMIN%')
            ->getQuery()
            ->getResult();

        return $this->render('admin/reports.html.twig', [
            'users'=>$users
        ]);
    }

    /**
     * @Route("/reports/prepare", name="reports_prepare")
     * @Method("POST")
     */
    public function prepareReportsAction(Request $request)
    {
        $this->checkUserAuth();

        $period = $request->request->get('period');
        $uids = $request->request->get('uids');

        if(
               $uids != ''
            && $period != ''
        ){
            $dates = explode(' - ', $period);

            $date_from  = \DateTime::createFromFormat('d.m.Y H:i:s', $dates[0] . ' 00:00:00');
            $date_to    = \DateTime::createFromFormat('d.m.Y H:i:s', $dates[1] . ' 23:59:59');

            $em = $this->getDoctrine()->getManager();

            //get managers deals data
            $sql = 'SELECT d.*, u.username FROM deal d LEFT JOIN fos_user u ON u.id = d.uid WHERE u.id IN ('.$uids.') AND d.updated_at >= \''.$date_from->format('Y-m-d H:i:s').'\' AND d.updated_at <= \''.$date_to->format('Y-m-d H:i:s').'\' ORDER BY d.updated_at DESC';
            $stmt = $em->getConnection()->prepare($sql);
            $stmt->execute();
            $deals_data = $stmt->fetchAll();

            //get current seed data
            $sql = 'SELECT s.* FROM seed_data s WHERE s.updated_at >= DATE_FORMAT(NOW(),"%Y-%m-%d 00:00:00") AND s.updated_at <= DATE_FORMAT(NOW(),"%Y-%m-%d 23:59:59") ORDER BY updated_at DESC LIMIT 1';
            $stmt = $em->getConnection()->prepare($sql);
            $stmt->execute();
            $seed_data = $stmt->fetchAll();

            if( !empty($seed_data) ){
                $seed_data = $seed_data[0];
            }
            else{
                //get latest seed data
                $sql = 'SELECT s.* FROM seed_data s ORDER BY updated_at DESC LIMIT 1';
                $stmt = $em->getConnection()->prepare($sql);
                $stmt->execute();
                $seed_data = $stmt->fetchAll();

                if( !empty($seed_data) ){
                    $seed_data = $seed_data[0];
                }
                else{
                    return new JsonResponse(['result'=>false,'msg'=>'Unable to retrieve latest seed data.']);
                }
            }

            $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

            $phpExcelObject->getProperties()
//            ->setCreator("liuggio")
//            ->setLastModifiedBy("Giulio De Donato")
                ->setTitle("Отчёт по сделкам")
//            ->setSubject("Office 2005 XLSX Test Document")
//            ->setDescription("Test document for Office 2005 XLSX, generated using PHP classes.")
//            ->setKeywords("office 2005 openxml php")
//            ->setCategory("Test result file")
            ;

            $i = 1;

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A' . $i, 'Менеджер')
                ->setCellValue('B' . $i, 'Номер сделки')
                ->setCellValue('C' . $i, 'Дата сделки')
                ->setCellValue('D' . $i, 'Цена закупки семечки на складе продавца, руб. без НДС на тонну')
                ->setCellValue('E' . $i, 'Стоимость доставки, руб. без НДС на тонну')
                ->setCellValue('F' . $i, 'Стоимость отгрузки, руб. без НДС на тонну')
                ->setCellValue('G' . $i, 'Стоимость хранения, руб. без НДС на тонну')
                ->setCellValue('H' . $i, 'Масличность семян подсолнечника, % от АСВ')
                ->setCellValue('I' . $i, 'Объём закупки, тонн')
                ->setCellValue('J' . $i, 'Комментарий')

                ->setCellValue('K' . $i, 'Логистика доставки семечки, руб. без НДС на тонну')
                ->setCellValue('L' . $i, 'Цена закупки семечки на заводе (цена+логистика), руб. без НДС на тонну')
                ->setCellValue('M' . $i, 'Цена закупки семечки на заводе (с учетом масличности и переработки), руб. без НДС на тонну')
                ->setCellValue('N' . $i, 'Коэффициент «Альфа»')
                ->setCellValue('O' . $i, 'Коэффициент «Омега»')
                ->setCellValue('P' . $i, 'Минимум «Омега»')
                ->setCellValue('Q' . $i, 'Превышение минимума «Омега»')
                ->setCellValue('R' . $i, 'Премия на 1 тонну за превышение коэф. «Омега», руб.')
            ;
            $i++;
            foreach( $deals_data as $deal ){

                $dealData = new \DateTime( $deal['updated_at'] );

                $deal_logistic_price = $deal['delivery_price'] + $deal['shipment_price'] + $deal['storage_price']*3;
                $deal_seed_purchase_price = ($deal['seed_price'] + $deal_logistic_price)*1.02;

                $deal['oil_content'] = $deal['oil_content']/100;
                if( $deal['oil_content'] > 0.48 ){
                    $price = ($deal['oil_content'] - 0.48)*1.5*$deal['seed_price'] + $deal['seed_price'];
                }
                else{
                    if( $deal['oil_content'] > 0.46 ){
                        $price = $deal['seed_price'];
                    }
                    else{
                        if( $deal['oil_content'] >= 0.43 ){
                            $price = $deal['seed_price'] - ( 0.46 - $deal['oil_content'] )*2*$deal['seed_price'];
                        }
                        else{
                            $price = $deal['seed_price'] - ( 0.06 + ( 0.43 - $deal['oil_content'] )*3 )*$deal['seed_price'];
                        }
                    }
                }
                $deal_seed_purchase_price_oil = ($price + $deal_logistic_price)*1.02 + $seed_data['processing_cost'];

                $omegaNumeratorOil = (intval($seed_data['oil_price']) - 15)*intval($seed_data['usdrub']) - 2000;
                $omegaNumeratorOilMeal = intval($seed_data['oilmeal_price'])*intval($seed_data['usdrub']) - 2000;

                $alpha = ($omegaNumeratorOil + $omegaNumeratorOilMeal)/$deal_seed_purchase_price;

                $oilYield        = $deal['oil_content']*100*0.91 - 1.2;
                $oilMealYield    = 81.5 - $oilYield;
                $omega = (($omegaNumeratorOil * $oilYield + $omegaNumeratorOilMeal * $oilMealYield) / 100) / $deal_seed_purchase_price_oil;

                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $deal['username'])
                    ->setCellValue('B' . $i, $deal['id'])
                    ->setCellValue('C' . $i, $dealData->format('H:i:s d.m.Y'))
                    ->setCellValue('D' . $i, $deal['seed_price'])
                    ->setCellValue('E' . $i, $deal['delivery_price'])
                    ->setCellValue('F' . $i, $deal['shipment_price'])
                    ->setCellValue('G' . $i, $deal['storage_price'])
                    ->setCellValue('H' . $i, $deal['oil_content'])
                    ->setCellValue('I' . $i, $deal['purchase_volume'])
                    ->setCellValue('J' . $i, $deal['comment'])

                    ->setCellValue('K' . $i, $deal_logistic_price)
                    ->setCellValue('L' . $i, $deal_seed_purchase_price)
                    ->setCellValue('M' . $i, $deal_seed_purchase_price_oil)
                    ->setCellValue('N' . $i, round($alpha, 2))
                    ->setCellValue('O' . $i, round($omega, 2))
                    ->setCellValue('P' . $i, $seed_data['minomega'])
                    ->setCellValue('Q' . $i, round($omega - $seed_data['minomega'], 2))
                    ->setCellValue('R' . $i, round($omega - $seed_data['minomega'], 2)*500)
                ;

                $i++;
            }

            $phpExcelObject->getActiveSheet()->setTitle('Отчёт по сделкам');
            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $phpExcelObject->setActiveSheetIndex(0);

            // create the writer
            $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel2007');

            $path = __DIR__.'/../../../web/reports/';
            $fileName = 'report_' . date('H_i_s_d_m_Y', time()) . '.xlsx';
            $writer->save($path.$fileName);

            // create the response
            $response = $this->get('phpexcel')->createStreamedResponse($writer);
            // adding headers
            $dispositionHeader = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $fileName
            );
            $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8');
            $response->headers->set('Pragma', 'public');
            $response->headers->set('Cache-Control', 'maxage=1');
            $response->headers->set('Content-Disposition', $dispositionHeader);

            return $response;
        }

        return new JsonResponse(['result'=>false]);
    }
}