<?php $this->layout("layouts/admin", ["title" => APPNAME]) ?>


<?php $this->start("page") ?>
<style>

</style>
<div class="container content p-4">
  <div class="tab-content">

    <!-- Dashboard Tab -->
    <div class="tab-pane pb-5 fade show active" id="dashboard">
      <h2 class="mb-4">TỔNG QUAN</h2>

      <?php if (AUTHGUARD()->user()->role !== 'khách hàng'): ?>

        <div class="alert text-center alert-success alert-dismissible fade show" role="alert">
          <h1>Chào mừng quay trở lại <?= $this->e(AUTHGUARD()->user()->role) ?></h1>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>

      <div class="row mb-4">
        <div class="col-md-3">
          <div class="card stat-card mb-3">
            <div class="card-body">
              <div class="d-flex justify-content-between">
                <div>
                  <h6 class="card-title text-muted">Phòng chờ nhận</h6>
                  <h2 class="mb-0">${givenRooms.size() }</h2>
                </div>
                <div class="bg-light p-3 rounded-circle">
                  <i class="bi bi-clipboard-check fs-3 text-primary"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card stat-card mb-3">
            <div class="card-body">
              <div class="d-flex justify-content-between">
                <div>
                  <h6 class="card-title text-muted">Đang sử dụng</h6>
                  <h2 class="mb-0">${occupiedRooms.size() }</h2>
                </div>
                <div class="bg-light p-3 rounded-circle">
                  <i class="bi bi-person-fill fs-3 text-success"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card stat-card mb-3">
            <div class="card-body">
              <div class="d-flex justify-content-between">
                <div>
                  <h6 class="card-title text-muted">Phòng chờ trả</h6>
                  <h2 class="mb-0">${checkOutRooms.size() }</h2>
                </div>
                <div class="bg-light p-3 rounded-circle">
                  <i class="bi bi-bell fs-3 text-danger"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card stat-card mb-3">
            <div class="card-body">
              <div class="d-flex justify-content-between">
                <div>
                  <h6 class="card-title text-muted">Doanh thu hôm nay</h6>
                  <h2 class="mb-0">${ revenue }M</h2>
                </div>
                <div class="bg-light p-3 rounded-circle">
                  <i class="bi bi-currency-dollar fs-3 text-warning"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row mb-4">
        <div class="col-md-8">
          <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
              <h5 class="mb-0">Feedback</h5>
            </div>
            <div class="card-body">
              <div class="table-responsive" style="max-height: 250px; overflow-y: auto; border: 1px solid #ddd;">
                <table class="table table-hover">
                  <thead class="text-center"
                    style="position: sticky; top: 0; background: white; z-index: 1; text-align: center;">
                    <tr>
                      <th>Mã phản hồi</th>
                      <th>Nội dung</th>
                      <th>Thời gian</th>
                      <th>Mã sử dụng dịch vụ</th>
                      <th>Mã khách hàng</th>
                    </tr>
                  </thead>
                  <tbody>
                    <c:forEach var="feedback" items="${feedbacks }" varStatus="status">
                      <tr>
                        <td>${feedback.feedbackId }</td>
                        <td>${feedback.content }</td>
                        <td>
                          <fmt:formatDate value="${feedback.feedbackTime }" pattern="dd/MM/yyyy HH:mm" />
                        </td>
                        <td>${feedback.useService.getUsId() }</td>
                        <td>${feedback.customer.getCustomerId() }</td>
                      </tr>
                    </c:forEach>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card mb-4">
            <div class="card-header bg-white">
              <h5 class="mb-0">Tình trạng phòng</h5>
            </div>
            <div class="card-body">
              <div class="mt-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <span>Có sẵn</span>
                  <span class="badge room-available px-3">${totalAvailable }</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <span>Đã đặt</span>
                  <span class="badge room-occupied px-3">${totalOccupied }</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <span>Bảo trì</span>
                  <span class="badge room-maintenance px-3">${totalMaintenance }</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6">
          <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
              <h5 class="mb-0">Check-in hôm nay</h5>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>Mã đặt phòng</th>
                      <th>Khách hàng</th>
                      <th>Phòng</th>
                      <th>Thời gian</th>
                      <th></th>
                    </tr>
                  </thead>

                  <tbody>
                    <c:forEach var="givenRoom" items="${givenRooms }" varStatus="status">
                      <tr>
                        <td>${givenRoom.rentId }</td>
                        <td>${givenRoom.customer.getCustomerName() }</td>
                        <td>${givenRoom.room.getRoomId() }</td>
                        <td>
                          <fmt:formatDate value="${givenRoom.rentalDate }" pattern="dd/MM/yyyy HH:mm" />
                        </td>
                        <td>

                          <form action="checkin" method="post">
                            <input type="text" hidden name="rentId" value="${givenRoom.rentId }">
                            <button class="btn btn-sm btn-outline-success">Check-in</button>
                          </form>
                        </td>
                      </tr>
                    </c:forEach>


                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
              <h5 class="mb-0">Check-out hôm nay</h5>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>Mã đặt phòng</th>
                      <th>Khách hàng</th>
                      <th>Phòng</th>
                      <th>Thời gian</th>
                      <th></th>
                    </tr>
                  </thead>

                  <tbody>
                    <c:forEach var="checkOutRoom" items="${checkOutRooms }" varStatus="status">
                      <tr>
                        <td>${checkOutRoom.rentId }</td>
                        <td>${checkOutRoom.customer.getCustomerName() }</td>
                        <td>${checkOutRoom.room.getRoomId() }</td>
                        <td>
                          <fmt:formatDate value="${checkOutRoom.rentalDate }" pattern="dd/MM/yyyy HH:mm" />
                        </td>
                        <td>
                          <form action="checkout" method="post">
                            <input type="text" hidden name="rentId" value=">${checkOutRoom.rentId }">
                            <input type="text" hidden name="roomId" value="${checkOutRoom.room.getRoomId() }">
                            <input type="text" hidden name="status" value="Available">
                            <button class="btn btn-sm btn-outline-success">Check-out</button>
                          </form>
                        </td>
                      </tr>
                    </c:forEach>

                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<?php $this->stop() ?>