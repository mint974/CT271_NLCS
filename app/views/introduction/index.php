<?php $this->layout("layouts/default", ["title" => APPNAME]) ?>

<?php $this->start("page") ?>
<style>
    /* .position-relative {
        width: 100%;
    } */

    .intro-card {
        position: absolute;
        width: 80%;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: rgba(232, 252, 207, 0.9);
        padding: 20px;
        z-index: 1;
    }

    .detailed-intro p,
    .intro-card p {
        text-align: justify;
    }

    .detailed-intro .card {
        background-color: #e8fccf;
    }

    .commit-intro {
        margin: 15px  0;
        padding: 20px 20px 50px 20px;
        background-color: #e8fccf;
       
    }

    .commit-intro img {
        width: 120px;
    }

    @media (max-width: 1200px) {
        .intro-card {
            position: static;
            width: 100%;
            transform: none;
            background-color: #e8fccf;
        }
    }

    .carousel-item img {
        width: 100%;
        height: auto;
        aspect-ratio: 12 / 6;
        object-fit: cover;
        object-position: center;
        border-radius: 20px;
    }

 /* Hiệu ứng trượt lên */
.slide-up {
    opacity: 0;
    transform: translateY(40px);
    will-change: opacity, transform;
    transition: transform 0.6s cubic-bezier(0.22, 1, 0.36, 1), opacity 0.6s ease-out;
}

.slide-up.visible {
    opacity: 1;
    transform: translateY(0);
}

</style>

<!-- Carousel hiển thị ảnh nền + nội dung -->

<div class="container intro-main pb-3">
    <!-- SECTION 1: CAROUSEL BANNER -->
    <div class="position-relative">
        <div id="carouselExampleSlidesOnly" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="/assets/image/introduction/intro_1.jpeg" class="d-block w-100 h-auto" alt="Fruits 1">
                </div>
                <div class="carousel-item">
                    <img src="/assets/image/introduction/intro_2.png" class="d-block w-100" alt="Fruits 2">
                </div>
                <div class="carousel-item">
                    <img src="/assets/image/introduction/intro_3.jpg" class="d-block w-100" alt="Fruits 3">
                </div>
            </div>
        </div>

        <div class="card intro-card text-center">
            <div class="card-header row justify-content-center">
                <img src="/assets/image/full-logo.png" class="col-sm-12 col-md-6" style="width: 150px;"
                    alt="Mint Fresh Fruit Logo">
                <h2 class="col-sm-12 col-md-6 align-self-center" style="color: #08a045; "><b>Mint Fresh Fruit</b></h2>
            </div>
            <div class="card-text fs-4">
                <p>
                    Mint Fresh Fruit được ra đời với sứ mệnh mang đến nguồn trái cây tươi ngon, sạch và an toàn cho mọi
                    gia đình.
                    Chúng tôi lựa chọn kỹ lưỡng từng loại trái cây từ các vùng trồng uy tín trong và ngoài nước, đảm bảo
                    chất lượng và độ tươi mới mỗi ngày.
                    Mint Fresh Fruit không chỉ bán trái cây, mà còn trao gửi sức khỏe, tình yêu thiên nhiên và cam kết
                    về chất lượng đến từng khách hàng.
                </p>
            </div>
        </div>
    </div>

    <!-- SECTION 2: INTRO DETAILS -->
    <div class="detailed-intro pb-2">
        <h1 class="text-center mb-4"><b>GIỚI THIỆU</b></h1>
        <div class="row row-cols-2 row-cols-md-3 g-6 mt-3 text-center">
            <div class="col">
                <div class="card h-100">
                    <div class="card-body">
                        <h3 class="card-title mt-3 mb-3"><b>PHƯƠNG CHÂM</b></h3>
                        <p class="card-text fs-5">
                            Mint Fresh Fruit hoạt động với phương châm “Tươi mỗi ngày – Sạch từng phút”. Chúng tôi đặt
                            khách
                            hàng vào vị trí trung tâm để phát triển dịch vụ.
                            Mỗi loại trái cây không chỉ đảm bảo chất lượng mà còn mang lại giá trị dinh dưỡng tối ưu cho
                            sức
                            khỏe người tiêu dùng.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card h-100">
                    <div class="card-body">
                        <h3 class="card-title mt-3 mb-3"><b>ĐỊNH HƯỚNG</b></h3>
                        <p class="card-text fs-5">
                            Mint Fresh Fruit không ngừng mở rộng chuỗi cung ứng, hợp tác với các nông trại uy tín và ứng
                            dụng công nghệ bảo quản hiện đại.
                            Chúng tôi hướng đến trở thành thương hiệu cung cấp trái cây sạch hàng đầu Việt Nam, góp phần
                            nâng cao chất lượng sống cho cộng đồng.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card h-100">
                    <div class="card-body">
                        <h3 class="card-title mt-3 mb-3"><b>SỰ KHÁC BIỆT</b></h3>
                        <p class="card-text fs-5">
                            Khác biệt của Mint đến từ quy trình kiểm soát chất lượng nghiêm ngặt, thái độ phục vụ tận
                            tâm và
                            trải nghiệm mua sắm hiện đại.
                            Từ website đến cửa hàng, khách hàng luôn cảm nhận được sự an tâm, tiện lợi và niềm vui trong
                            mỗi
                            lần mua sắm.
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- SECTION 3: COMMITMENTS -->
    <div class="commit-intro rounded ">
        <h1 class="text-center mb-4"><b>MINT FRESH FRUIT CAM KẾT</b></h1>
        <div class="row justify-content-around text-center">
            <div class="col-md-4 col-sm-6 col-xl-2 mt-2 mb-2">
                <img src="/assets/image/introduction/fast-delivery.png" alt="">
                <p class="mt-2 fs-5">Giao hàng nhanh</p>
            </div>
            <div class="col-md-4 col-sm-6 col-xl-2 mt-2 mb-2">
                <img src="/assets/image/introduction/fresh.png" alt="">
                <p class="mt-2 fs-5">Trái cây tươi mỗi ngày</p>
            </div>
            <div class="col-md-4 col-sm-6 col-xl-2 mt-2 mb-2">
                <img src="/assets/image/introduction/guarantee.png" alt="">
                <p class="mt-2 fs-5">100% nguồn gốc rõ ràng</p>
            </div>
            <div class="col-md-4 col-sm-6 col-xl-2 mt-2 mb-2">
                <img src="/assets/image/introduction/return.png" alt="">
                <p class="mt-2 fs-5">Đổi trả dễ dàng</p>
            </div>
            <div class="col-md-4 col-sm-6 col-xl-2 mt-2 mb-2">
                <img src="/assets/image/introduction/secure.png" alt="">
                <p class="mt-2 fs-5">Bảo mật thông tin khách hàng</p>
            </div>
        </div>
    </div>

    <div class=" quality-commitment pb-4">
        <div class="container">
            <div class="logo">
                <img src="/assets/image/full-logo.png" class="logo" alt="">
            </div>
            <div class="row mt-3 m-1">
                <div class="image-box col-12 col-lg-6 p-2">
                    <div class="row">
                        <div class="col-12 col-sm-6 d-flex flex-column align-items-center justify-content-center">
                            <img src="/assets/image/index/quality1.jpg" alt="Quality 1">
                        </div>
                        <div class="col-12 col-sm-6 d-flex flex-column align-items-center">
                            <img src="/assets/image/index/quality2.jpg" class="m-3" alt="Quality 2">
                            <img src="/assets/image/index/quality3.jpg" class="m-3" alt="Quality 3">
                        </div>
                    </div>
                    <div class="organic">
                        <img src="/assets/image/index/quality4.png" alt="Quality 4">
                    </div>
                </div>

                <div
                    class="quality-commitment-box text-center col-12 col-lg-6 d-flex flex-column justify-content-evenly align-items-center">
                    <h2 class="fs-1 mt-3 mb-3"><b>CAM KẾT CHẤT LƯỢNG</b></h2>
                    <p class="fs-4 mt-3 mb-3">MINT tuyển chọn kỹ lưỡng trái cây từ các nông trại uy tín, đảm bảo sản
                        phẩm luôn
                        tươi
                        ngon và an toàn cho sức khỏe người tiêu dùng. Chúng tôi không sử dụng hóa chất bảo quản hay
                        thuốc trừ sâu,
                        và
                        tự hào mang đến cho bạn những trái cây tốt nhất.</p>
                    <h3 class="mt-3 mb-3"><b>Cung cấp 100% thực phẩm hữu cơ và lành mạnh.</b></h3>
                    <img class="mt-3 mb-3" src="/assets/image/index/MinhTan.png" alt="Minh Tan">
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const elementsToAnimate = [
            ...document.querySelectorAll('.detailed-intro .card'),
            ...document.querySelectorAll('.commit-intro .col-md-4, .commit-intro .col-xl-2'),
            ...document.querySelectorAll('.quality-commitment .image-box, .quality-commitment .quality-commitment-box')
        ];

        // Áp dụng class và delay dần
        elementsToAnimate.forEach((el, index) => {
            el.classList.add('slide-up');
            el.style.transitionDelay = `${index * 0.05}s`; // delay tăng nhẹ nhàng
        });

        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.2
        });

        elementsToAnimate.forEach(el => {
            observer.observe(el);
        });
    });
</script>



<?php $this->stop() ?>