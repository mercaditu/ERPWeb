<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Input;
use Response;
use View;

class kpicontroller extends Controller
{
    public function Execute_KPI($Key, $StartDate, $EndDate){
        //search for JsonKey
        $Key = __NAMESPACE__. '\kpicontroller::' . $Key;
        return call_user_func_array($Key, array($StartDate , $EndDate));
    }

  public function SalesXDay($StartDate, $EndDate){
      $Date = array();
      $Sales = array();
      $query = " select
	             (sid.unit_price * sid.quantity) as Sales,
                 date(trans_date) as Date,
                 b.name as Branch
                 from sales_invoice as si
                 inner join sales_invoice_detail as sid on si.id_sales_invoice = sid.id_sales_invoice
                 left join app_branch as b
                 on si.id_branch = b.id_branch
                 where si.id_company = 1
                 and si.trans_date between '" . $StartDate . "' and '" . $EndDate . "'
                 and si.id_branch = 1
                 group by date(trans_date)
                 order by date(trans_date)";
      $data = DB::select(DB::raw($query));
      foreach($data as $entry){
        $Sales[] = $entry->Sales;
        $Date[] = $entry->Date;
        $Branch[] = $entry->Branch;
      }
      $response = array("Sales"=>$Sales, "Date"=>$Date);
      return Response::json($response);
  }

  //sum(sid.quantity * sid.unit_price) as Sales,
  //sum(sid.quantity * sid.unit_cost) as Costs

  public function Top10Sales($StartDate, $EndDate){
    $Sales = array();
    $Costs = array();
    $Item = array();
    $query = "select
              i.name as Item,
              sum(sid.quantity * sid.unit_price) as Sales,
              sum(sid.quantity * sid.unit_cost) as Costs
              from
              sales_invoice as si
              left join sales_invoice_detail as sid on si.id_sales_invoice = sid.id_sales_invoice
              left join items as i on i.id_item = sid.id_item
              where
              si.trans_date between '" . $StartDate . "' and '" . $EndDate . "' and si.id_company = 1
              group by sid.id_item
              order by sum(sid.quantity) desc limit 10";
    $data = DB::select(DB::raw($query));
    foreach($data as $entry){
      $Sales[] = $entry->Sales;
      $Costs[] = $entry->Costs;
      $Item[] = $entry->Item;
    }
    $response = array("Sales"=>$Sales, "Costs"=>$Costs,"Item"=>$Item);
    return Response::json($response);
  }

  public function SalesByTag_Percent($StartDate, $EndDate){
    $Tag = array();
    $Percentage = array();
    $query = "select
	          it.name as Tag,
              round((sum(quantity * unit_price)/(
                                select sum(quantity * unit_price)
                                       from sales_invoice as si1
                                       left join sales_invoice_detail as sid1
                                       on si1.id_sales_invoice = sid1.id_sales_invoice
                                       where si1.trans_date
                                       between '" . $StartDate . "' and '" . $EndDate . "')*100),2) as Percentage
              from sales_invoice as si
              left join sales_invoice_detail as sid
              on sid.id_sales_invoice = si.id_sales_invoice
              left join items as i
              on i.id_item = sid.id_item
              left join item_tag_detail as itd
              on itd.id_item = i.id_item
              left join item_tag as it
              on it.id_tag = itd.id_tag
              where si.trans_date between '".$StartDate."' and '".$EndDate."'
              and si.id_company = 1
              group by it.id_tag
              order by it.id_tag";
    $data = DB::select(DB::raw($query));
    foreach($data as $entry){
      $Tag[] = $entry->Tag;
      $Percentage[] = $entry->Percentage;
    }
    return Response::json(array("Tag"=>$Tag,"Percentage"=>$Percentage));
  }

  public function TotalSales($StartDate, $EndDate){
    $query = "select ifnull(sum(quantity * unit_price * vatco.coef),0) as Sales
              from sales_invoice as si
              left join sales_invoice_detail as sd
              on si.id_sales_invoice = sd.id_sales_invoice
              left join
              (select app_vat_group.id_vat_group,sum(app_vat.coefficient) + 1 as coef
              from app_vat_group
              left join app_vat_group_details
              on app_vat_group.id_vat_group = app_vat_group_details.id_vat_group
              left join app_vat
              on app_vat_group_details.id_vat = app_vat.id_vat
              group by app_vat_group.id_vat_group) as vatco
              on sd.id_vat_group = vatco.id_vat_group
              where si.trans_date between '" . $StartDate . "' and '" . $EndDate . "'";
    $data = DB::select(DB::raw($query));
    return Response::json($data);
  }

  public function getconfig(){
    $json_file = file_get_contents(storage_path() . "/app/config/components.json");
    return $json_file;
  }

  public function AvgSalesPerInv($StartDate,$EndDate){
    $query = "select ifnull(avg(salesperinvoice),0) as averagesalesperinv
              from (select sum(sid.quantity*sid.unit_price)as salesperinvoice , si.id_sales_invoice
              from sales_invoice as si
              left join sales_invoice_detail as sid
              on si.id_sales_invoice = sid.id_sales_invoice
              where si.trans_date between '" . $StartDate . "' and '" . $EndDate . "'
              group by si.id_sales_invoice) as QuantityPerInvoice";
    $data = DB::select(DB::raw($query));
    return Response::json($data);
  }

  public function AvgQuantityPerInv($StartDate,$EndDate){
    $query = "select ifnull(avg(qtyperinvoice),0) as averagequantityperinv
              from (select sum(sid.quantity)as qtyperinvoice , si.id_sales_invoice
              from sales_invoice as si
              left join sales_invoice_detail as sid
              on si.id_sales_invoice = sid.id_sales_invoice
              where si.trans_date between '" . $StartDate . "' and '" . $EndDate . "'
              group by si.id_sales_invoice) as QuantityPerInvoice";
    $data = DB::select(DB::raw($query));
    return Response::json($data);
  }

  public function Sales_ByFootTraffic($StartDate, $EndDate){
    $Sales = 0;
    $FootTraffic = 0;

    $queryfootfall = "select ifnull(sum(quantity),0) as FootTraffic
                      from app_branch_walkins
                      where (start_date >= '" . $StartDate . "' and start_date <= '" . $EndDate . "' ) and
                            (end_date >= '" . $StartDate . "' and end_date <= '" . $EndDate . "' )";

    $querytotalsales = "select sum(sid.quantity * sid.unit_price) as Sales
                        from sales_invoice as si
                        left join sales_invoice_detail as sid
                        on si.id_sales_invoice = sid.id_sales_invoice
                        where si.trans_date between '" . $StartDate . "' and '" . $EndDate . "'";

    $footfalldata = DB::select(DB::raw($queryfootfall));
    $salesdata = DB::select(DB::raw($querytotalsales));

    foreach ($footfalldata as $ff) {
      $FootTraffic = $ff->FootTraffic;
    }

    foreach ($salesdata as $s) {
      $Sales = $s->Sales;
    }

    if($FootTraffic == 0) {
        return 0;
    }
    else {
        return $Sales/$FootTraffic;
    }
  }
}
