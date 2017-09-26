<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AdminController extends Controller
{
    /**
     * @Route("/reports/list", name="reports_list")
     */
    public function reportsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $users = $em
            ->getRepository('AppBundle:User')
            ->createQueryBuilder('u')
            ->getQuery()
            ->getResult();

        $_users = [];
        foreach ($users as $user){
            /* @var $user \AppBundle\Entity\User */
            $_users[] = [
                'id'=>$user->getId()
                ,'login'=>$user->getEmail()
                ,'name'=>$user->getUsername()
                ,'roles'=>$user->getRoles()
                ,'active'=>($user->isEnabled()?1:0)
            ];
        }

        return $this->render('admin/reports.html.twig', [
            'users'=>$_users
        ]);
    }

    /**
     * @Route("/reports/prepare", name="reports_prepare")
     */
    public function prepareReportsAction(Request $request)
    {
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

            $count = count($deals_data);
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
            ;
            $i++;
            foreach( $deals_data as $deal ){

                $dealData = new \DateTime( $deal['updated_at'] );

                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $deal['username'])
                    ->setCellValue('B' . $i, $count)
                    ->setCellValue('C' . $i, $dealData->format('H:i:s d.m.Y'))
                    ->setCellValue('D' . $i, $deal['seed_price'])
                    ->setCellValue('E' . $i, $deal['delivery_price'])
                    ->setCellValue('F' . $i, $deal['shipment_price'])
                    ->setCellValue('G' . $i, $deal['storage_price'])
                    ->setCellValue('H' . $i, $deal['oil_content'])
                    ->setCellValue('I' . $i, $deal['purchase_volume'])
                    ->setCellValue('J' . $i, $deal['comment'])
                ;

                $count--;
                $i++;
            }

            $phpExcelObject->getActiveSheet()->setTitle('Отчёт по сделкам');
            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $phpExcelObject->setActiveSheetIndex(0);

            // create the writer
            $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel2007');

            $path = __DIR__.'\..\..\..\web\reports\\';
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