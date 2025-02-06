<?php

namespace Modules\Icommercepayu\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\Icommerce\Entities\PaymentMethod;

class IcommercepayuSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    Model::unguard();

    if (!is_module_enabled('Icommercepayu')) {
      //$this->command->alert('This module: Icommercepayu is DISABLED!! , please enable the module and then run the seed');
      exit();
    }

    //Validation if the module has been installed before
    $name = config('asgard.icommercepayu.config.paymentName');
    $paymentMethod = PaymentMethod::where('name', $name)->first();
    $PaymentMethodRepository = app('Modules\Icommerce\Repositories\PaymentMethodRepository');

    if (!$paymentMethod) {
      $options['init'] = "Modules\Icommercepayu\Http\Controllers\Api\IcommercePayuApiController";
      $options['mainimage'] = null;
      $options['merchantId'] = '508029';
      $options['apiLogin'] = 'pRRXKOl8ikMmt9u';
      $options['apiKey'] = '4Vj8eK4rloUd272L48hsrarnUA';
      $options['accountId'] = '512321';
      $options['mode'] = 'sandbox';
      $options['test'] = 1;
      $options['minimunAmount'] = 15000;
      $options['showInCurrencies'] = ['COP'];

      $titleTrans = 'icommercepayu::icommercepayus.single';
      $descriptionTrans = 'icommercepayu::icommercepayus.description';

      $params = [
        'name' => $name,
        'status' => 1,
        'options' => $options,
        'organization_id' => isset(tenant()->id) ? tenant()->id : null,
      ];
      $paymentMethod = PaymentMethod::create($params);

      $this->addTranslation($paymentMethod, 'en', $titleTrans, $descriptionTrans);
      $this->addTranslation($paymentMethod, 'es', $titleTrans, $descriptionTrans);
    } else {
      if ($paymentMethod->description != trans('icommercepayu::icommercepayus.iaDescription', [], locale())) {
        $data = array(
          'es' => ['description' => trans('icommercepayu::icommercepayus.iaDescription', [], 'es')],
          'en' => ['description' => trans('icommercepayu::icommercepayus.iaDescription', [], 'en')]
        );
        $paymentMethod = $PaymentMethodRepository->update($paymentMethod, $data);
        //Instance file service
        $fileService = app("Modules\Media\Services\FileService");
        //Instance the file path
        $filePath = 'Modules/Icommercepayu/Resources/img/payu_default.png';
        if (Storage::disk('local')->exists($filePath)) {
          // Obtener el contenido del archivo
          $fileContents = Storage::disk('local')->get($filePath);
          // Convertir el archivo a base64
          $base64File = base64_encode($fileContents);
          //Get base64 file
          $uploadedFile = getUploadedFileFromBase64($base64File);
          //Create file
          $file = $fileService->store($uploadedFile, 0, 'publicmedia');
          //set file if
          $fileId = $file->id;
          //Sync file id
          $paymentMethod->files()->attach($fileId, ['zone' => 'mainimage']);
        }
      }
      //It doesn't work in jobs
      //$this->command->alert("This method has already been installed !!");
    }
  }

  /*
  * Add Translations
  * PD: New Alternative method due to problems with astronomic translatable
  **/
  public function addTranslation($paymentMethod, $locale, $title, $description)
  {
    \DB::table('icommerce__payment_method_translations')->insert([
      'title' => trans($title, [], $locale),
      'description' => trans($description, [], $locale),
      'payment_method_id' => $paymentMethod->id,
      'locale' => $locale,
    ]);
  }
}
