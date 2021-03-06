<?php

namespace Modules\Icommercepayu\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Icommerce\Entities\PaymentMethod;

class IcommercepayuDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      
      Model::unguard();
  
      $name = config('asgard.icommercepayu.config.paymentName');
      $result = PaymentMethod::where('name',$name)->first();

      if(!$result){

        $options['init'] = "Modules\Icommercepayu\Http\Controllers\Api\IcommercePayuApiController";
        $options['mainimage'] = null;
        $options['merchantId'] = "508029";
        $options['apiLogin'] = "pRRXKOl8ikMmt9u";
        $options['apiKey'] = "4Vj8eK4rloUd272L48hsrarnUA";
        $options['accountId'] = "512321";
        $options['mode'] = "sandbox";
        $options['test'] = 1;
        $options['minimunAmount'] = 15000;
  
        $titleTrans = 'icommercepayu::icommercepayus.single';
        $descriptionTrans = 'icommercepayu::icommercepayus.description';
  
        foreach (['en', 'es'] as $locale) {
    
          if($locale=='en'){
            $params = array(
              'title' => trans($titleTrans),
              'description' => trans($descriptionTrans),
              'name' => $name,
              'status' => 1,
              'options' => $options
            );
      
            $paymentMethod = PaymentMethod::create($params);
      
          }else{
      
            $title = trans($titleTrans,[],$locale);
            $description = trans($descriptionTrans,[],$locale);
      
            $paymentMethod->translateOrNew($locale)->title = $title;
            $paymentMethod->translateOrNew($locale)->description = $description;
      
            $paymentMethod->save();
          }
    
        }// Foreach

      }else{

        $this->command->alert("This method has already been installed !!");

      }
   
    }
}