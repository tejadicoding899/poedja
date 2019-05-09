<!DOCTYPE html>
<html>
<head>
    <title>Analyze Sample</title>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
</head>
<body>

    <?php
    /**----------------------------------------------------------------------------------
    * Microsoft Developer & Platform Evangelism
    *
    * Copyright (c) Microsoft Corporation. All rights reserved.
    *
    * THIS CODE AND INFORMATION ARE PROVIDED "AS IS" WITHOUT WARRANTY OF ANY KIND, 
    * EITHER EXPRESSED OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE IMPLIED WARRANTIES 
    * OF MERCHANTABILITY AND/OR FITNESS FOR A PARTICULAR PURPOSE.
    *----------------------------------------------------------------------------------
    * The example companies, organizations, products, domain names,
    * e-mail addresses, logos, people, places, and events depicted
    * herein are fictitious.  No association with any real company,
    * organization, product, domain name, email address, logo, person,
    * places, or events is intended or should be inferred.
    *----------------------------------------------------------------------------------
    **/

    /** -------------------------------------------------------------
    # Azure Storage Blob Sample - Demonstrate how to use the Blob Storage service. 
    # Blob storage stores unstructured data such as text, binary data, documents or media files. 
    # Blobs can be accessed from anywhere in the world via HTTP or HTTPS. 
    #
    # Documentation References: 
    #  - Associated Article - https://docs.microsoft.com/en-us/azure/storage/blobs/storage-quickstart-blobs-php 
    #  - What is a Storage Account - http://azure.microsoft.com/en-us/documentation/articles/storage-whatis-account/ 
    #  - Getting Started with Blobs - https://azure.microsoft.com/en-us/documentation/articles/storage-php-how-to-use-blobs/
    #  - Blob Service Concepts - http://msdn.microsoft.com/en-us/library/dd179376.aspx 
    #  - Blob Service REST API - http://msdn.microsoft.com/en-us/library/dd135733.aspx 
    #  - Blob Service PHP API - https://github.com/Azure/azure-storage-php
    #  - Storage Emulator - http://azure.microsoft.com/en-us/documentation/articles/storage-use-emulator/ 
    #
    **/

    require_once 'vendor/autoload.php';
    require_once "./random_string.php";

    use MicrosoftAzure\Storage\Blob\BlobRestProxy;
    use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
    use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
    use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
    use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;

    // $connectionString = "DefaultEndpointsProtocol=https;AccountName=".getenv('ACCOUNT_NAME').";AccountKey=".getenv('ACCOUNT_KEY');
    $connectionString = "DefaultEndpointsProtocol=https;AccountName="."dicodingwebappstorage".";AccountKey="."jD9ciw5KsIWoQn6gcC+z1v+REA8M2kD5KnItopqEZnHvGGH5V/OiOw6ZFJ3ajMmgDTcICK8zCVIophrYDBHDjw==";

    // Create blob client.
    $blobClient = BlobRestProxy::createBlobService($connectionString);

    $fileToUpload = "samsung.jpg";

    if (!isset($_GET["Cleanup"])) {
        // Create container options object.
        $createContainerOptions = new CreateContainerOptions();

        // Set public access policy. Possible values are
        // PublicAccessType::CONTAINER_AND_BLOBS and PublicAccessType::BLOBS_ONLY.
        // CONTAINER_AND_BLOBS:
        // Specifies full public read access for container and blob data.
        // proxys can enumerate blobs within the container via anonymous
        // request, but cannot enumerate containers within the storage account.
        //
        // BLOBS_ONLY:
        // Specifies public read access for blobs. Blob data within this
        // container can be read via anonymous request, but container data is not
        // available. proxys cannot enumerate blobs within the container via
        // anonymous request.
        // If this value is not specified in the request, container data is
        // private to the account owner.
        $createContainerOptions->setPublicAccess(PublicAccessType::CONTAINER_AND_BLOBS);

        // Set container metadata.
        $createContainerOptions->addMetaData("key1", "jD9ciw5KsIWoQn6gcC+z1v+REA8M2kD5KnItopqEZnHvGGH5V/OiOw6ZFJ3ajMmgDTcICK8zCVIophrYDBHDjw==");
        $createContainerOptions->addMetaData("key2", "H6Pk89Zc9k6DWLj369YHYkBr6GANkgxYwyJHzZTi4CCyF4mcuwQubMkWbl2sqpUA3mpCUs8A+YexvHyjvdRMdQ==");

        $containerName = "blockblobs".generateRandomString();

        try {
            // Create container.
            $blobClient->createContainer($containerName, $createContainerOptions);

            // Getting local file so that we can upload it to Azure
            // $myfile = fopen($fileToUpload, "w") or die("Unable to open file!");
            // fclose($myfile);
            
            # Upload file as a block blob
            echo "Uploading BlockBlob: ".PHP_EOL;
            echo $fileToUpload;
            echo "<br />";
            
            $content = fopen($fileToUpload, "r");

            // $imageBinaryString = file_get_contents($fileToUpload);
            // $base64_imageString = base64_encode($imageBinaryString);

            // $content = fopen('data:image/jpeg;base64,' . $base64_imageString,'r');
            // $content = fopen('data:image/jpeg;base64,' . $base64_imageString,'r');

            //Upload blob
            $blobClient->createBlockBlob($containerName, $fileToUpload, $content);

            // List blobs.
            $listBlobsOptions = new ListBlobsOptions();
            $listBlobsOptions->setPrefix("samsung");

            echo "These are the blobs present in the container: ";

            do{
                $result = $blobClient->listBlobs($containerName, $listBlobsOptions);
                foreach ($result->getBlobs() as $blob)
                {
                    echo $blob->getName().": ".$blob->getUrl()."<br />";
                    echo "<img src='" .$blob->getUrl(). "' alt='".$blob->getName()."'>" ."<br />";
                    echo "<input value='" .$blob->getUrl(). "' id='imagetoanalyze' />" ."<br />";
                }
            
                $listBlobsOptions->setContinuationToken($result->getContinuationToken());
            } while($result->getContinuationToken());
            echo "<br />";
            

            // Get blob.
            echo "This is the content of the blob uploaded: " . $fileToUpload;
            // $blob = $blobClient->getBlob($containerName, $fileToUpload);
            // fpassthru($blob->getContentStream());
            echo "<br />";
        }
        catch(ServiceException $e){
            // Handle exception based on error codes and messages.
            // Error codes and messages are here:
            // http://msdn.microsoft.com/library/azure/dd179439.aspx
            $code = $e->getCode();
            $error_message = $e->getMessage();
            echo "error 1 ".$code.": ".$error_message."<br />";
        }
        catch(InvalidArgumentTypeException $e){
            // Handle exception based on error codes and messages.
            // Error codes and messages are here:
            // http://msdn.microsoft.com/library/azure/dd179439.aspx
            $code = $e->getCode();
            $error_message = $e->getMessage();
            echo "error 2 ".$code.": ".$error_message."<br />";
        }
    } 
    else 
    {

        try{
            // Delete container.
            echo "Deleting Container".PHP_EOL;
            echo $_GET["containerName"].PHP_EOL;
            echo "<br />";
            $blobClient->deleteContainer($_GET["containerName"]);
        }
        catch(ServiceException $e){
            // Handle exception based on error codes and messages.
            // Error codes and messages are here:
            // http://msdn.microsoft.com/library/azure/dd179439.aspx
            $code = $e->getCode();
            $error_message = $e->getMessage();
            echo $code.": ".$error_message."<br />";
        }
    }
    ?>

<!-- imagetoanalyze -->
    <div>
        <button type="button" onclick="analyze()">Analyze Image</button>
        <div id="jsonOutput" style="width:600px; display:table-cell;">
            Response:
            <br><br>
            <textarea id="responseTextArea" class="UIInput"
                    style="width:580px; height:400px;display:none;"></textarea>
            
            <textarea id="responseTextArea2" class="UIInput"
                style="width:580px;"></textarea>
        </div>
    </div>

    <form method="post" action="index.php?Cleanup&containerName=<?php echo $containerName; ?>">
        <button type="submit">Press to clean up all resources created by this sample</button>
    </form>

    <script type="text/javascript">
        function analyze() {
            // **********************************************
            // *** Update or verify the following values. ***
            // **********************************************
    
            // Replace <Subscription Key> with your valid subscription key.
            var subscriptionKey = "0fabe5b99d374c1092e2c9c2554fa48c";
    
            // You must use the same Azure region in your REST API method as you used to
            // get your subscription keys. For example, if you got your subscription keys
            // from the West US region, replace "westcentralus" in the URL
            // below with "westus".
            //
            // Free trial subscription keys are generated in the "westus" region.
            // If you use a free trial subscription key, you shouldn't need to change
            // this region.
            var uriBase =
                "https://southeastasia.api.cognitive.microsoft.com/vision/v2.0/analyze";

            // Request parameters.
            var params = {
                "visualFeatures": "Categories,Description,Color",
                "details": "",
                "language": "en",
            };

            // Display the image.
            var sourceImageUrl = document.getElementById("imagetoanalyze").value;

            // Make the REST API call.
            $.ajax({
                url: uriBase + "?" + $.param(params),

                // Request headers.
                beforeSend: function(xhrObj){
                    xhrObj.setRequestHeader("Content-Type","application/json");
                    xhrObj.setRequestHeader(
                        "Ocp-Apim-Subscription-Key", subscriptionKey);
                },

                type: "POST",

                // Request body.
                data: '{"url": ' + '"' + sourceImageUrl + '"}',
            })

            .done(function(data) {
                // Show formatted JSON on webpage.
                $("#responseTextArea").val(JSON.stringify(data, null, 2));
                $("#responseTextArea2").val(data.description.captions[0].text);
            })

            .fail(function(jqXHR, textStatus, errorThrown) {
                // Display error message.
                var errorString = (errorThrown === "") ? "Error. " :
                    errorThrown + " (" + jqXHR.status + "): ";
                errorString += (jqXHR.responseText === "") ? "" :
                    jQuery.parseJSON(jqXHR.responseText).message;
                alert(errorString);
            });
        }
    </script>
</body>
</html>