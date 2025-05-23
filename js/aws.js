var albumBucketName = 'testdarstage';
var bucketRegion = 'us-west-2';
var IdentityPoolId = 'us-west-2:38d42401-44e8-4b51-a938-904f283e323a';
var creds = new AWS.CognitoIdentityCredentials({
    IdentityPoolId: IdentityPoolId
});
AWS.config.credentials = creds;
AWS.config.region = bucketRegion;

var s3 = new AWS.S3({
    apiVersion: '2006-03-01',
    params: { Bucket: albumBucketName }
});

function fileUpload() {
    var upload = document.getElementById("file");
    var file = upload.files[0];
    var uuid = upload.getAttribute('data-index')
    var title = "SupportFormation";
    console.log(upload, file);
    if (file) {
        uploadToS3(file, title, uuid);
    };
}

function uploadToS3(file, key, uuid) {
    if (uuid != "") {
        var objKey = key + uuid;
        var params = {
            Key: objKey,
            ContentType: file.type,
            Body: file,
            ACL: 'public-read'
        };
        console.log(params, s3);
        s3.putObject(params).send(function (err, data) {
            console.log("putObject.send");
            if (err) {
                console.log("erreur : " + err);
            } else {
                console.log("https://testdarstage.s3.us-west-2.amazonaws.com/" + key + uuid);
                document.getElementById("upload").style.display = "block";
            }
        });
    }

}


