@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-2"></div>
    <div class="col-md-8 justify-content-center">
        <div class="card">
            <form class="form-group mx-auto" id="profileImageForm" method="POST" action="{{ route('profile.uploadProfileImage') }}" enctype="multipart/form-data">
                @csrf
                <a href="#" class="upload-button mx-auto" title="Change profile image">
                    @if ($user->profile_image)
                        <img id="profileImage" class="img-thumbnail mx-auto" style="width:200px;height:200px" src="{{ asset('storage/profile_images/' . $user->profile_image) }}" alt="Profile Image">
                    @else
                        <img id="profileImage" class="img-thumbnail mx-auto" style="width:200px;height:200px" src="{{ asset('storage/profile_images/default.png') }}" alt="Default Image">
                    @endif
                    <input type="file" name="profile_image" id="profileImageInput" style="display: none;">
                </a>
                <!-- Rest of the form -->
            </form>
                
            <!-- Display the user's name -->
            <h2 class="mx-auto">{{ $user->name }}</h2>

            <script>
                const uploadButton = document.querySelector('.upload-button');
                const imageInput = document.querySelector('#imageInput');
                const profileImage = document.querySelector('#profileImage');

                profileImage.addEventListener('click', function() {
                profileImageInput.click();
                });

                // Add an event listener to the file input element
                profileImageInput.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        var reader = new FileReader();

                        reader.onload = function(e) {
                            profileImage.src = e.target.result;
                            // Trigger the form submission to upload the image
                            profileImageForm.submit();
                        }

                        reader.readAsDataURL(this.files[0]);
                    }       
                });

                // JavaScript approach
                // document.getElementById('uploadButton').addEventListener('click', function() {
                //     document.getElementById('uploadFormContainer').style.display = 'block';
                // });

                // jQuery approach
                // $('#uploadButton').click(function() {
                //     $('#uploadFormContainer').show();
                // });
            </script>

            
        </div>
    </div>
    <div class="col-md-2"></div>
</div>

@endsection