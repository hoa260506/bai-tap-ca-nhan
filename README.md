# CHỦ ĐỀ 6: HỆ THỐNG QUẢN LÝ SỰ KIỆN/CÂU LẠC BỘ SINH VIÊN

## 1. Danh sách thành viên và phân công công việc

| Thành viên | Nhiệm vụ | Nhánh Git | Minh chứng |
|---|---|---|---|
| Trương Mỹ Hoa | Dashboard Ban tổ chức; CRUD sự kiện; quản lý trạng thái; xem người đăng ký; điểm danh; thống kê sự kiện | `feature/organizer-events` | Controller, View, Repository, truy vấn thống kê, kiểm thử phân quyền, commit |
| Trần Thị Bình Minh | Danh sách và chi tiết sự kiện; tìm kiếm, lọc theo ngày và câu lạc bộ | `feature/event-discovery` | View danh sách/chi tiết, Controller, CSS/JS, kiểm thử tìm kiếm và lọc |
| Đặng Thu Thảo | Đăng ký, hủy đăng ký, trang Đăng ký của tôi, tích hợp Fetch API | `feature/event-registration` | Controller, View, endpoint JSON, JavaScript Fetch API, kiểm thử đăng ký |
| Nguyễn Phương Anh | Dashboard Admin; quản lý câu lạc bộ, tài khoản và vai trò | `feature/admin-clubs-users` | Controller, View, truy vấn PDO, kiểm thử phân quyền và dữ liệu |
| Bùi Mai Phương | Quản lý toàn bộ sự kiện; Dashboard Admin; thống kê; tổng hợp README, checklist kiểm thử và slide | `feature/admin-events-stats` | Controller, View, truy vấn thống kê, README, checklist tối thiểu 15 trường hợp, slide, commit |

## 2. Thiết kế biểu mẫu và kiểm tra dữ liệu

### 2.1. Biểu mẫu thêm sự kiện

**Thành viên phụ trách:** Trương Mỹ Hoa .

| Trường dữ liệu | Quy tắc kiểm tra |
|---|---|
| Tên sự kiện | Bắt buộc, tối đa 200 ký tự |
| Mô tả | Không bắt buộc |
| Địa điểm | Không bắt buộc, tối đa 255 ký tự |
| Thời gian bắt đầu | Bắt buộc, phải là ngày giờ hợp lệ |
| Thời gian kết thúc | Không bắt buộc; nếu có phải sau thời gian bắt đầu |
| Hạn đăng ký | Không bắt buộc; nếu có phải trước hoặc bằng thời gian bắt đầu |
| Số lượng tối đa | Bắt buộc, là số nguyên lớn hơn hoặc bằng 1 |
| Trạng thái | Chỉ nhận `nhap` hoặc `cong_khai` |

### 2.2. Quy tắc xử lý và bảo mật

- Kiểm tra người dùng đã đăng nhập và có quyền phù hợp.
- Kiểm tra mã CSRF khi gửi biểu mẫu.
- Kiểm tra dữ liệu ở phía máy chủ, không chỉ dựa vào thuộc tính HTML.
- Sử dụng câu lệnh SQL có tham số để hạn chế SQL Injection.
- Từ chối dữ liệu không hợp lệ và không lưu sự kiện khi kiểm tra thất bại.
- Kiểm tra quyền sở hữu sự kiện trước khi cho phép sửa, xóa, hủy hoặc điểm danh.
- Không cho phép người dùng thực hiện chức năng vượt quá quyền được cấp.

### 2.3. Route của biểu mẫu thêm sự kiện

| Phương thức | Route | Chức năng |
|---|---|---|
| GET | `/Nhom-4-LTW/public/index.php?trang=them_su_kien` | Hiển thị biểu mẫu thêm sự kiện |
| POST | `/Nhom-4-LTW/public/index.php?trang=luu_su_kien` | Kiểm tra dữ liệu và lưu sự kiện |

### 2.4. Biểu mẫu danh sách sự kiện

**Thành viên phụ trách:** Trần Thị Bình Minh
Trường dữ liệu và quy tắc kiểm tra
- **Từ khóa tìm kiếm:** Có thể để trống; loại bỏ khoảng trắng ở đầu và cuối; giới hạn tối đa 100 ký tự.
- **Câu lạc bộ:** Người dùng có thể chọn một câu lạc bộ trong danh sách hoặc chọn “Tất cả” để xem sự kiện của mọi câu lạc bộ.
- **Ngày tổ chức:** Ngày được chọn phải hợp lệ. Nếu lọc theo khoảng ngày, ngày bắt đầu không được lớn hơn ngày kết thúc.
- **Trạng thái sự kiện:** Chỉ chấp nhận các trạng thái có trong hệ thống hoặc chọn “Tất cả”.
- **Mã sự kiện:** Phải là mã hợp lệ và tồn tại trong cơ sở dữ liệu. Nếu không tìm thấy, hệ thống hiển thị thông báo phù hợp.

### 2.5. Quy tắc nghiệp vụ

- **Hiển thị danh sách sự kiện:** Chỉ hiển thị các sự kiện phù hợp với từ khóa tìm kiếm và điều kiện lọc mà người dùng đã chọn.
- **Tìm kiếm sự kiện:** Không phân biệt khoảng trắng thừa ở đầu và cuối từ khóa. Nếu không có kết quả phù hợp, hệ thống thông báo “Không tìm thấy sự kiện phù hợp”.
- **Lọc sự kiện:** Khi chọn câu lạc bộ, ngày tổ chức hoặc trạng thái, danh sách phải được cập nhật theo điều kiện tương ứng. Có thể kết hợp nhiều điều kiện lọc.
- **Xem chi tiết sự kiện:** Khi người dùng chọn một sự kiện, hệ thống hiển thị thông tin chi tiết của đúng sự kiện đó. Nếu sự kiện không tồn tại, hệ thống thông báo và không hiển thị thông tin sai.
- **Trạng thái đăng ký:** Sự kiện đã đóng đăng ký hoặc đã hết hạn đăng ký không được tiếp tục đăng ký. Việc xử lý đăng ký cần thống nhất với thành viên phụ trách chức năng đăng ký sự kiện.
- **Số lượng chỗ còn lại:** Số chỗ còn lại được hiển thị dựa trên dữ liệu của hệ thống và không được nhỏ hơn 0.
- **An toàn dữ liệu:** Dữ liệu tìm kiếm và các tham số được gửi lên hệ thống phải được kiểm tra. Khi truy vấn cơ sở dữ liệu, sử dụng câu lệnh có tham số để hạn chế nguy cơ SQL Injection.

### 2.6. Route của biểu mẫu danh sách sự kiện

- **Trang chủ / danh sách sự kiện:** `index.php` — Hiển thị danh sách sự kiện đang có trong hệ thống.
- **Tìm kiếm và lọc sự kiện:** `index.php?keyword=...&club_id=...&status=...` — Tìm kiếm theo từ khóa và lọc theo câu lạc bộ, trạng thái sự kiện.
- **Lọc theo ngày tổ chức:** `index.php?start_date=...&end_date=...` — Hiển thị các sự kiện trong khoảng thời gian được chọn.
- **Xem chi tiết sự kiện:** `event-detail.php?id=...` — Hiển thị thông tin chi tiết của sự kiện theo mã sự kiện.
- **Đăng ký tham gia sự kiện:** `event-register.php?id=...` — Chuyển đến chức năng đăng ký sự kiện, phối hợp với thành viên phụ trách nghiệp vụ đăng ký.

#### Quy tắc sử dụng route 
- Các tham số tìm kiếm và lọc được truyền qua URL bằng phương thức GET.
- Mã sự kiện phải được kiểm tra trước khi truy vấn và hiển thị chi tiết.
- Nếu mã sự kiện không hợp lệ hoặc không tồn tại, hệ thống hiển thị thông báo phù hợp.
- Chức năng đăng ký phải kiểm tra trạng thái đăng ký và số chỗ còn lại trước khi tiếp nhận yêu cầu.
- Các route trên là đường dẫn dự kiến, cần thống nhất với cấu trúc thư mục và cách định tuyến hiện tại của dự án trước khi tích hợp. 

### 2.7. Biểu mẫu đăng ký và hủy đăng ký sự kiện

**Thành viên phụ trách:** Đặng Thu Thảo.

#### 2.7.1. Biểu mẫu/màn hình

- **Màn hình danh sách sự kiện:** `public/danh_sach_su_kien.php` — Hiển thị danh sách sự kiện và nút Đăng ký, tích hợp Fetch API để gửi yêu cầu đăng ký.
- **Màn hình Đăng ký của tôi:** `public/thanh_vien/dang_ky_cua_toi.php` — Hiển thị danh sách sự kiện mà người dùng đã đăng ký.
- **Hộp thoại xác nhận hủy đăng ký:** Sử dụng Modal được xây dựng bằng HTML, CSS và JavaScript để xác nhận trước khi hủy đăng ký.

#### 2.7.2. Các trường dữ liệu

| Trường dữ liệu | Ý nghĩa và quy tắc kiểm tra |
|---|---|
| `su_kien_id` | ID sự kiện được chọn; phải hợp lệ và tồn tại trong cơ sở dữ liệu. |
| `dang_ky_id` | ID lượt đăng ký cần hủy; phải hợp lệ và tồn tại. |
| `nguoi_dung_id` | ID người dùng lấy từ Session; không tin tưởng ID người dùng do phía trình duyệt tự gửi lên. |
| `action` | Xác định thao tác cần thực hiện, gồm đăng ký hoặc hủy đăng ký theo quy ước của API. |

#### 2.7.3. Điều kiện đăng ký sự kiện

- Sự kiện phải tồn tại và có trạng thái `cong_khai`.
- Thời điểm đăng ký phải trước thời gian bắt đầu sự kiện.
- Sự kiện phải còn thời hạn đăng ký theo quy định của hệ thống.
- Số lượng người đăng ký hợp lệ phải nhỏ hơn `so_luong_toi_da`.
- Không cho phép đăng ký trùng nếu người dùng đã có lượt đăng ký chưa bị hủy.
- Nếu lượt đăng ký trước đó có trạng thái `da_huy`, hệ thống có thể cho phép đăng ký lại theo quy tắc nghiệp vụ.
- Nếu điều kiện đăng ký không được đáp ứng, hệ thống từ chối yêu cầu và trả về thông báo phù hợp.

#### 2.7.4. Điều kiện hủy đăng ký

- Chỉ cho phép hủy lượt đăng ký có trạng thái `da_dang_ky`.
- Người dùng chỉ được hủy lượt đăng ký thuộc về chính mình.
- Yêu cầu hủy phải được thực hiện trước thời gian bắt đầu sự kiện ít nhất 24 giờ (86.400 giây).
- Không cho phép hủy nếu lượt đăng ký không tồn tại, không thuộc người dùng hiện tại hoặc không đáp ứng điều kiện thời gian.
- Sau khi hủy thành công, trạng thái lượt đăng ký được cập nhật thành `da_huy`.

#### 2.7.5. Quy tắc kiểm tra đăng ký trùng, hết hạn và hết chỗ

- **Kiểm tra đăng ký trùng:** Đối chiếu `nguoi_dung_id` và `su_kien_id` trong bảng `dang_ky`. Nếu đã có lượt đăng ký chưa bị hủy, hệ thống từ chối đăng ký mới.
- **Kiểm tra hết chỗ:** Đếm các lượt đăng ký có trạng thái `da_dang_ky` hoặc `da_tham_gia`. Nếu số lượng đã đạt hoặc vượt `so_luong_toi_da`, hệ thống từ chối đăng ký.
- **Kiểm tra thời hạn:** Đối chiếu thời điểm hiện tại với thời gian bắt đầu và hạn đăng ký của sự kiện.
- **Kiểm tra quyền hủy:** Đối chiếu người dùng hiện tại với chủ sở hữu lượt đăng ký trước khi cập nhật trạng thái.
- **Kiểm tra dữ liệu:** Xác thực ID và các tham số nhận từ yêu cầu trước khi truy vấn cơ sở dữ liệu.
- **Hạn chế tranh chấp dữ liệu:** Sử dụng Transaction và khóa dòng `FOR UPDATE` phù hợp khi kiểm tra sức chứa và tạo lượt đăng ký, sau khi xác nhận cơ chế này đã được triển khai đúng trong mã nguồn.

#### 2.7.6. Quy tắc xử lý và bảo mật

- Kiểm tra người dùng đã đăng nhập trước khi đăng ký hoặc hủy đăng ký.
- Không tin tưởng `nguoi_dung_id` do phía trình duyệt gửi lên; lấy danh tính từ Session phía máy chủ.
- Sử dụng câu lệnh SQL có tham số để hạn chế SQL Injection.
- Kiểm tra quyền sở hữu lượt đăng ký khi hủy.
- Kiểm tra mã CSRF hoặc cơ chế bảo vệ tương đương đối với yêu cầu thay đổi dữ liệu.
- API trả về dữ liệu JSON để giao diện hiển thị thông báo thành công hoặc lỗi.
- Không trả về thông tin lỗi nội bộ hoặc dữ liệu nhạy cảm của hệ thống cho người dùng.

#### 2.7.7. Route/API

| Phương thức | Route/API | Chức năng |
|---|---|---|
| GET | `/Nhom-4-LTW/public/danh_sach_su_kien.php` | Hiển thị danh sách sự kiện và nút đăng ký |
| GET | `/Nhom-4-LTW/public/thanh_vien/dang_ky_cua_toi.php` | Hiển thị danh sách đăng ký cá nhân |
| POST | `/Nhom-4-LTW/public/api/dang_ky.php` | Tiếp nhận yêu cầu đăng ký sự kiện thông qua Fetch API |
| POST | `/Nhom-4-LTW/public/api/dang_ky.php` | Tiếp nhận yêu cầu hủy đăng ký thông qua Fetch API |

#### 2.7.8. Minh chứng

- **Thư mục lưu ảnh:** `docs/minh_chung_nguoi_3`.

### 2.8. Biểu mẫu và quy tắc quản lý câu lạc bộ, tài khoản Admin

**Thành viên phụ trách:** Nguyễn Phương Anh

**Biểu mẫu/màn hình:**

- Dashboard Admin: màn hình tổng quan dành cho quản trị viên.
- Màn hình danh sách câu lạc bộ: hiển thị các câu lạc bộ hiện có trong hệ thống.
- Biểu mẫu thêm câu lạc bộ: nhập thông tin để tạo câu lạc bộ mới.
- Biểu mẫu sửa câu lạc bộ: cập nhật thông tin của câu lạc bộ đã tồn tại.
- Màn hình danh sách tài khoản: hiển thị danh sách tài khoản trong hệ thống.
- Các chức năng cập nhật tài khoản và vai trò cần được đối chiếu với mã nguồn và kiểm thử trước khi xác nhận hoàn thành.

**Các trường dữ liệu:**

| Nhóm dữ liệu | Trường dữ liệu | Quy tắc kiểm tra |
|---|---|---|
| Câu lạc bộ | Mã câu lạc bộ | Do hệ thống quản lý; cần bảo đảm tính duy nhất |
| Câu lạc bộ | Tên câu lạc bộ | Bắt buộc nhập; kiểm tra độ dài và dữ liệu đầu vào |
| Câu lạc bộ | Mô tả | Có thể để trống nếu nghiệp vụ cho phép |
| Câu lạc bộ | Mã người quản lý | Không bắt buộc nếu nghiệp vụ cho phép; nếu nhập phải kiểm tra người quản lý có tồn tại và hợp lệ |
| Tài khoản | Mã tài khoản | Do hệ thống quản lý; xác định đúng tài khoản cần thao tác |
| Tài khoản | Thông tin tài khoản | Kiểm tra các trường theo biểu mẫu và cấu trúc cơ sở dữ liệu thực tế |
| Tài khoản | Vai trò | Chỉ chấp nhận các vai trò được hệ thống quy định |

**Quy tắc quản lý câu lạc bộ:**

- Kiểm tra tên câu lạc bộ không được để trống trước khi thêm hoặc cập nhật.
- Kiểm tra mã người quản lý nếu được nhập; không chấp nhận mã không tồn tại hoặc không hợp lệ.
- Kiểm tra câu lạc bộ có tồn tại trước khi thực hiện cập nhật hoặc xóa.
- Sử dụng câu lệnh SQL có tham số để hạn chế nguy cơ SQL Injection.
- Thực hiện thao tác xóa bằng phương thức POST, không xóa dữ liệu chỉ bằng cách truy cập đường dẫn GET.
- Xử lý trường hợp không thể xóa câu lạc bộ do bị ràng buộc khóa ngoại; thông báo rõ kết quả cho người dùng.
- Hiển thị thông báo phù hợp sau khi thêm, sửa hoặc xóa thành công hay thất bại.
- Kiểm tra dữ liệu ở phía máy chủ, không chỉ dựa vào validation trên giao diện.

**Quy tắc quản lý tài khoản và vai trò:**

- Sử dụng trang `tai_khoan.php` và Controller `QuanLyTaiKhoanController.php` để đối chiếu các chức năng quản lý tài khoản.
- Kiểm tra tài khoản có tồn tại trước khi thực hiện thao tác cập nhật.
- Kiểm tra vai trò được chọn có thuộc danh sách vai trò hợp lệ của hệ thống hay không.
- Chỉ cho phép cập nhật thông tin và vai trò theo đúng quyền được cấp.
- Kiểm tra kết quả cập nhật và hiển thị thông báo phù hợp.
- Các thao tác cụ thể về cập nhật tài khoản và vai trò cần được kiểm tra trong mã nguồn và kiểm thử thực tế trước khi ghi nhận hoàn thành.

**Quy tắc phân quyền:**

- Các trang và chức năng quản trị phải giới hạn cho tài khoản có quyền Admin.
- Kiểm tra quyền truy cập ở phía máy chủ trước khi thực hiện thao tác quản trị.
- Không chỉ ẩn nút hoặc đường dẫn trên giao diện để thay thế cho việc kiểm tra quyền.
- Không cho phép người dùng tự ý thay đổi vai trò hoặc thực hiện chức năng vượt quá quyền được cấp.
- Cần kiểm thử cả trường hợp Admin truy cập hợp lệ và tài khoản không có quyền Admin cố truy cập trực tiếp vào route quản trị.

**Route:**

Các route dự kiến theo cấu trúc `page` được xác định trong phần thiết kế. Cần đối chiếu với `public/index.php` để xác nhận đường dẫn thực tế của dự án.

| Phương thức | Route dự kiến | Chức năng |
|---|---|---|
| GET | `/?page=dashboard` | Hiển thị Dashboard Admin |
| GET | `/?page=cau-lac-bo` | Hiển thị danh sách câu lạc bộ |
| GET/POST | `/?page=them-cau-lac-bo` | Hiển thị biểu mẫu và xử lý thêm câu lạc bộ |
| GET | `/?page=sua-cau-lac-bo&id={id}` | Hiển thị biểu mẫu sửa câu lạc bộ |
| POST | `/?page=sua-cau-lac-bo&id={id}` | Xử lý cập nhật câu lạc bộ |
| POST | `/?page=xoa-cau-lac-bo` | Xử lý xóa câu lạc bộ |
| GET | `/?page=tai-khoan` | Hiển thị danh sách tài khoản |

**Minh chứng:**

- Controller quản lý Admin: `app/Controllers/QuanLyAdminController.php`.
- Controller quản lý tài khoản: `app/Controllers/QuanLyTaiKhoanController.php`.
- Model câu lạc bộ: `app/Models/CauLacBo.php`.
- Các View quản trị tương ứng trong `app/Views/admin/`.
- Ảnh chụp Dashboard Admin, danh sách câu lạc bộ, biểu mẫu thêm/sửa câu lạc bộ và danh sách tài khoản.
- Kết quả kiểm thử validation, phân quyền, thao tác xóa và trường hợp bị ràng buộc khóa ngoại.
- Lịch sử commit trên nhánh `feature/admin-clubs-users`.

### 2.9. Biểu mẫu và quy tắc quản lý sự kiện toàn hệ thống

**Thành viên phụ trách:** Bùi Mai Phương

**Biểu mẫu/màn hình:**
- Dashboard Admin: tổng quan tình hình hoạt động của hệ thống.
- Màn hình quản lý sự kiện toàn hệ thống: xem danh sách, thêm, cập nhật, thay đổi trạng thái, xóa và xem chi tiết sự kiện.
- Màn hình thống kê: tổng hợp dữ liệu sự kiện và lượt đăng ký.
- Màn hình quản lý tài khoản Ban tổ chức nếu được tích hợp trong phạm vi chức năng Admin.

**Các trường dữ liệu:**

| Nhóm dữ liệu | Trường dữ liệu | Quy tắc kiểm tra |
|---|---|---|
| Thông tin sự kiện | Mã sự kiện | Được hệ thống quản lý; không trùng lặp |
| Thông tin sự kiện | Tên sự kiện | Không để trống; kiểm tra độ dài theo cấu trúc cơ sở dữ liệu |
| Thông tin sự kiện | Mô tả | Có thể để trống nếu nghiệp vụ cho phép |
| Thông tin sự kiện | Thời gian tổ chức | Phải là ngày giờ hợp lệ |
| Thông tin sự kiện | Địa điểm | Kiểm tra độ dài và dữ liệu đầu vào |
| Trạng thái | Trạng thái sự kiện | Chỉ chấp nhận các trạng thái được hệ thống quy định |
| Thống kê | Số lượng sự kiện | Tính từ dữ liệu sự kiện trong cơ sở dữ liệu |
| Thống kê | Lượt đăng ký | Tính từ dữ liệu đăng ký, tránh đếm trùng bản ghi |
| Thống kê | Số liệu theo trạng thái | Phân loại theo trạng thái thực tế của sự kiện |

**Quy tắc quản lý sự kiện toàn hệ thống:**
- Chỉ tài khoản có quyền Admin mới được truy cập các chức năng quản trị.
- Kiểm tra dữ liệu đầu vào ở phía máy chủ trước khi thêm hoặc cập nhật sự kiện.
- Sử dụng câu lệnh SQL có tham số để hạn chế SQL Injection.
- Kiểm tra sự kiện có tồn tại trước khi xem chi tiết, cập nhật trạng thái hoặc xóa.
- Không thực hiện thao tác nếu dữ liệu không hợp lệ hoặc thao tác thất bại.
- Hiển thị thông báo phù hợp sau mỗi thao tác thành công hoặc thất bại.
- Không cho phép người dùng thực hiện chức năng vượt quá quyền được cấp.

**Quy tắc thống kê:**
- Số lượng sự kiện được tổng hợp từ dữ liệu sự kiện trong cơ sở dữ liệu.
- Số lượt đăng ký được tính từ bảng đăng ký theo đúng quan hệ giữa sự kiện và người đăng ký.
- Số lượng sự kiện theo trạng thái phải được phân loại theo dữ liệu thực tế.
- Không tự tạo số liệu minh họa và trình bày chúng như dữ liệu thật.
- Kiểm tra kết quả truy vấn bằng cách đối chiếu với dữ liệu trong cơ sở dữ liệu.
- Trường hợp không có dữ liệu, giao diện cần hiển thị số 0 hoặc thông báo phù hợp.

**Route:**

Các route dưới đây được xác định theo tên `action` trong `public/index.php`:

| Phương thức | Route | Chức năng |
|---|---|---|
| GET | `public/index.php?action=quan-ly-su-kien` | Hiển thị danh sách sự kiện toàn hệ thống |
| GET/POST | `public/index.php?action=them-su-kien` | Chức năng thêm sự kiện; cần xác nhận cách xử lý GET/POST trong Controller |
| GET/POST | `public/index.php?action=cap-nhat-su-kien` | Chức năng cập nhật sự kiện; cần xác nhận cách xử lý GET/POST |
| GET/POST | `public/index.php?action=doi-trang-thai-su-kien` | Thay đổi trạng thái sự kiện; cần xác nhận phương thức thực tế |
| GET/POST | `public/index.php?action=xoa-su-kien` | Xóa sự kiện; cần xác nhận phương thức thực tế |
| GET | `public/index.php?action=chi-tiet-su-kien` | Xem chi tiết sự kiện |
| GET | `public/index.php?action=thong-ke` | Hiển thị trang thống kê |
| GET | `public/index.php` | Đi vào action mặc định `quan-ly-su-kien`; chưa xác nhận có Dashboard Admin riêng |

**Minh chứng:**
- Mã nguồn Controller xử lý chức năng quản lý sự kiện và thống kê.
- Các View tương ứng với danh sách, chi tiết sự kiện và trang thống kê.
- Ảnh chụp giao diện sau khi chạy ứng dụng.
- Ảnh kết quả truy vấn hoặc dữ liệu đối chiếu trong cơ sở dữ liệu.
- Checklist kiểm thử và lịch sử commit trên nhánh `feature/admin-events-stats`.


## 3. Kết quả kiểm thử form thêm và sửa sự kiện

**Thành viên phụ trách:** Trương Mỹ Hoa .

| STT | Trường hợp kiểm thử | Kết quả mong đợi | Kết quả thực tế |
|---|---|---|---|
| 1 | Mở form thêm sự kiện | Form hiển thị đầy đủ các trường | Đạt |
| 2 | Nhập dữ liệu hợp lệ và lưu | Sự kiện được lưu thành công | Đạt |
| 3 | Bỏ trống tên sự kiện | Hệ thống từ chối dữ liệu không hợp lệ | Đạt |
| 4 | Nhập số lượng tối đa bằng 0 | Hệ thống từ chối dữ liệu không hợp lệ | Đạt |
| 5 | Thời gian kết thúc trước thời gian bắt đầu | Hệ thống từ chối dữ liệu không hợp lệ | Đạt |
| 6 | Mở form sửa sự kiện | Dữ liệu hiện tại được hiển thị | Đạt |
| 7 | Sửa thông tin sự kiện hợp lệ | Thông tin sự kiện được cập nhật | Đạt |
| 8 | Truy cập chức năng bằng tài khoản không đủ quyền | Hệ thống từ chối truy cập | Đạt |


## 4. Wireframe và giao diện

### 4.1. Trương Mỹ Hoa Trang – Ban tổ chức

- **Dashboard Ban tổ chức:** `app/Views/ban_to_chuc/trang_chu.php`
- **Danh sách sự kiện:** `app/Views/ban_to_chuc/danh_sach_su_kien.php`
- **Form thêm sự kiện:** `app/Views/ban_to_chuc/them_su_kien.php`
- **Form sửa sự kiện:** `app/Views/ban_to_chuc/sua_su_kien.php`
- **Chi tiết sự kiện:** `app/Views/ban_to_chuc/chi_tiet_su_kien.php`
- **Danh sách người đăng ký và điểm danh:** `app/Views/ban_to_chuc/danh_sach_dang_ky.php`
- **Vị trí lưu ảnh/tài liệu minh chứng:** 

### 4.2. Trần Thị Bình Minh

- **Màn hình danh sách sự kiện**:Wireframe gồm danh sách/thẻ sự kiện với tên sự kiện, câu lạc bộ tổ chức, thời gian, địa điểm, số chỗ và trạng thái.
- **Màn hình chi tiết sự kiện**:Wireframe hiển thị tên sự kiện, đơn vị tổ chức, thời gian, địa điểm, hạn đăng ký, số chỗ, mô tả và nút đăng ký (phối hợp với thành viên phụ trách đăng ký).
- **Giao diện tìm kiếm và lọc**:Tìm kiếm theo từ khóa; lọc theo câu lạc bộ, ngày tổ chức và trạng thái sự kiện.
- **Wireframe Figma**: https://www.figma.com/design/qmhpkTAKk4V1wn8RcFlxtm

### 4.3. Đặng Thu Thảo

- Màn hình đăng ký sự kiện:public/danh_sach_su_kien.php (Tích hợp Fetch API và nút Đăng ký).
- Màn hình Đăng ký của tôi:public/thanh_vien/dang_ky_cua_toi.php (Hiển thị danh sách cá nhân).
- Giao diện hủy đăng ký:Sử dụng Modal xác nhận (tự viết CSS/JS) thay vì hộp thoại mặc định của trình duyệt.
- Vị trí lưu ảnh/tài liệu minh chứng:C:\Users\Thu Thao\Documents\GitHub\Nhom-4-LTW\docs\minh_chung_nguoi_3

### 4.4. Nguyễn Phương Anh

- Dashboard Admin:`app/Views/admin/dashboard.php`
- Màn hình quản lý câu lạc bộ:`app/Views/admin/cau_lac_bo.php`
- Màn hình quản lý tài khoản:`app/Views/admin/tai_khoan.php`
- Form thêm câu lạc bộ: `app/Views/admin/them_cau_lac_bo.php`
- Form sửa câu lạc bộ: `app/Views/admin/sua_cau_lac_bo.php`
- Controller: `app/Controllers/QuanLyAdminController.php` và `app/Controllers/QuanLyTaiKhoanController.php`; Model câu lạc bộ: `app/Models/CauLacBo.php`
- Vị trí lưu ảnh/tài liệu minh chứng:bổ sung đường dẫn ảnh chụp giao diện sau khi lưu ảnh vào thư mục minh chứng của nhóm.

### 4.5. Bùi Mai Phương

- **Dashboard Admin:** Giao diện tổng quan, thể hiện các thông tin quản trị và số liệu nổi bật của hệ thống. Cần xác nhận đường dẫn View thực tế trước khi ghi vào báo cáo.
- **Màn hình quản lý sự kiện toàn hệ thống:** `app/Views/admin/su_kien.php`
- **Màn hình chi tiết sự kiện:** `app/Views/admin/chi_tiet_su_kien.php`
- **Màn hình thống kê:** `app/Views/admin/thong_ke.php`
- **Màn hình quản lý tài khoản:** `app/Views/admin/tai_khoan.php`
- **Vị trí lưu ảnh/tài liệu minh chứng đề xuất:**
  - `docs/minh_chung_sv5/dashboard_admin/`
  - `docs/minh_chung_sv5/quan_ly_su_kien/`
  - `docs/minh_chung_sv5/thong_ke/`
  - `docs/minh_chung_sv5/kiem_thu/`

## 5. Danh sách route của hệ thống

Mỗi thành viên bổ sung các route thực tế do mình phụ trách sau khi kiểm tra mã nguồn. Không ghi route chưa được triển khai hoặc chưa xác nhận.

### 5.1. Trương Mỹ Hoa 

| Phương thức | Route | Chức năng | Kết quả kiểm tra |
|---|---|---|---|
| GET | `/Nhom-4-LTW/public/index.php?trang=them_su_kien` | Hiển thị form thêm sự kiện | Đạt |
| POST | `/Nhom-4-LTW/public/index.php?trang=luu_su_kien` | Lưu sự kiện mới | Đạt |
|  |  | Sửa sự kiện | Đạt |
|  |  | Xóa sự kiện | Đạt |
|  |  | Hủy sự kiện | Đạt |
|  |  | Xem chi tiết sự kiện | Đạt |
|  |  | Xem danh sách đăng ký | Đạt |
|  |  | Điểm danh | Đạt |
|  |  | Thống kê sự kiện | Đạt |

### 5.2. Trần Thị Bình Minh

| Phương thức | Route | Chức năng | Kết quả kiểm tra |
|---|---|---|---|
| GET | `/Nhom-4-LTW/public/index.php?trang=su-kien` | Hiển thị danh sách sự kiện công khai | Cú pháp PHP đạt; cần kiểm thử trên XAMPP |
| GET | `/Nhom-4-LTW/public/index.php?trang=chi-tiet&id=1` | Hiển thị chi tiết sự kiện theo ID | Cú pháp PHP đạt; cần kiểm thử trên XAMPP |
| GET | `/Nhom-4-LTW/public/index.php?trang=su-kien&tu_khoa=...&cau_lac_bo_id=...&ngay_bat_dau=YYYY-MM-DD&ngay_ket_thuc=YYYY-MM-DD` | Tìm kiếm/lọc theo từ khóa, câu lạc bộ và khoảng ngày | Cú pháp PHP đạt; cần kiểm thử trên XAMPP |

### 5.3. Đặng Thu Thảo

| Phương thức | Route/API | Chức năng | Kết quả kiểm tra |
| POST | /public/api/dang_ky.php | Xử lý Đăng ký sự kiện (Fetch API) | Đạt |
| POST | /public/api/dang_ky.php | Xử lý Hủy đăng ký (Fetch API) | Đạt |
| GET | /public/thanh_vien/dang_ky_cua_toi.php | Hiển thị danh sách đăng ký cá nhân | Đạt |
| POST | /public/api/dang_ky.php | Trả về Endpoint JSON (Xử lý lỗi, thành công) | Đạt |

### 5.4. Nguyễn Phương Anh

| Phương thức | Route | Chức năng | Kết quả kiểm tra |
|---|---|---|---|
| GET | `/?page=dashboard` | Dashboard Admin, hiển thị tổng số câu lạc bộ, tài khoản và sự kiện | Đã mở được trang; cần đối chiếu số liệu khi có dữ liệu mẫu |
| GET | `/?page=cau-lac-bo` | Danh sách câu lạc bộ | Đạt |
| GET/POST | `/?page=them-cau-lac-bo` | Hiển thị form và xử lý thêm câu lạc bộ | Đã triển khai; kiểm thử thêm để ghi nhận đầy đủ |
| GET/POST | `/?page=sua-cau-lac-bo&id={id}` | Hiển thị form và cập nhật câu lạc bộ | Đạt theo kiểm tra giao diện |
| POST | `/?page=xoa-cau-lac-bo` | Xóa câu lạc bộ, xử lý lỗi ID và ràng buộc khóa ngoại | Xóa thành công đã kiểm tra; trường hợp bị khóa ngoại chưa kiểm thử trực tiếp |
| GET | `/?page=tai-khoan` | Danh sách tài khoản | Có route/view trong dự án; cần kiểm thử thực tế |
| — | Chưa triển khai/được xác nhận | Cập nhật tài khoản và vai trò | Chưa có thao tác cập nhật tài khoản hoặc vai trò trong controller đã đối chiếu |

### 5.5. Bùi Mai Phương

| Phương thức | Route | Chức năng | Kết quả kiểm tra |
|---|---|---|---|
| GET | `public/index.php?action=quan-ly-su-kien` | Danh sách sự kiện toàn hệ thống | Chưa kiểm thử |
| GET/POST | `public/index.php?action=them-su-kien` | Thêm sự kiện | Chưa kiểm thử |
| GET/POST | `public/index.php?action=cap-nhat-su-kien` | Cập nhật sự kiện | Chưa kiểm thử |
| GET/POST | `public/index.php?action=doi-trang-thai-su-kien` | Thay đổi trạng thái sự kiện | Chưa kiểm thử |
| GET/POST | `public/index.php?action=xoa-su-kien` | Xóa sự kiện | Chưa kiểm thử |
| GET | `public/index.php?action=chi-tiet-su-kien` | Xem chi tiết sự kiện | Chưa kiểm thử |
| GET | `public/index.php?action=thong-ke` | Xem thống kê | Chưa kiểm thử |

## 6. Checklist kiểm thử toàn hệ thống

| STT | Thành viên | Chức năng kiểm thử | Kết quả mong đợi | Kết quả thực tế | Minh chứng |
|---|---|---|---|---|---|
| 1 | SV1 | Mở form thêm sự kiện | Form hiển thị đúng | Đạt | |
| 2 | SV1 | Lưu sự kiện hợp lệ | Sự kiện được lưu | Đạt | |
| 3 | SV1 | Bỏ trống tên sự kiện | Từ chối dữ liệu | Đạt | |
| 4 | SV1 | Nhập sức chứa bằng 0 | Từ chối dữ liệu | Đạt | |
| 5 | SV1 | Thời gian kết thúc không hợp lệ | Từ chối dữ liệu | Đạt | |
| 6 | SV1 | Sửa sự kiện hợp lệ | Dữ liệu được cập nhật | Đạt | |
| 7 | SV1 | Kiểm tra quyền truy cập | Từ chối truy cập trái phép | Đạt | |
| 8 | SV2 | Kiểm tra cú pháp PHP toàn bộ file | Không có lỗi cú pháp | Đạt | |
| 9 | SV2 | Xem danh sách sự kiện trên XAMPP | Danh sách hiển thị đúng dữ liệu CSDL | Chưa chạy được trong môi trường kiểm tra; cần xác minh trên XAMPP | |
| 10 | SV2 | Xem chi tiết bằng ID hợp lệ/không hợp lệ | Hiển thị đúng sự kiện hoặc trả thông báo 404 | Chưa kiểm thử tích hợp | |
| 11 | SV2 | Tìm kiếm và lọc theo từ khóa/câu lạc bộ/ngày | Kết quả đáp ứng đủ điều kiện; dữ liệu sai bị từ chối | Chưa kiểm thử tích hợp | |
| 12 | SV3 | Đăng ký sự kiện còn chỗ | Đăng ký thành công | Đạt |  |
| 13 | SV3 | Đăng ký trùng sự kiện | Từ chối đăng ký trùng | Đạt |  |
| 14 | SV3 | Hủy đăng ký hợp lệ | Đăng ký được hủy | Đạt |  |
| 15 | SV3 | Đăng ký khi hết chỗ/đã đóng | Từ chối đăng ký | Đạt |  |
| 16 | SV4 | Quản lý câu lạc bộ | Thao tác đúng quyền | Đạt |  |
| 17 | SV4 | Quản lý tài khoản | Dữ liệu được xử lý đúng | Chưa kiểm thử |  |
| 18 | SV4 | Truy cập chức năng Admin bằng tài khoản thường | Từ chối truy cập | Chưa kiểm thử |  |
| 19 | SV5 – Bùi Mai Phương | Xem Dashboard Admin | Hiển thị đúng dữ liệu tổng quan | Chưa kiểm thử | |
| 20 | SV5 – Bùi Mai Phương | Xem danh sách sự kiện toàn hệ thống | Danh sách khớp với cơ sở dữ liệu | Chưa kiểm thử | |
| 21 | SV5 – Bùi Mai Phương | Thêm và cập nhật sự kiện | Dữ liệu được lưu chính xác | Chưa kiểm thử | |
| 22 | SV5 – Bùi Mai Phương | Thay đổi trạng thái sự kiện | Trạng thái thay đổi đúng nghiệp vụ | Chưa kiểm thử | |
| 23 | SV5 – Bùi Mai Phương | Xóa sự kiện | Hệ thống xử lý đúng và có thông báo | Chưa kiểm thử | |
| 24 | SV5 – Bùi Mai Phương | Xem thống kê số lượng sự kiện | Số liệu chính xác | Chưa kiểm thử | |
| 25 | SV5 – Bùi Mai Phương | Xem thống kê lượt đăng ký | Số liệu chính xác, không đếm trùng | Chưa kiểm thử | |
| 26 | SV5 – Bùi Mai Phương | Kiểm tra quyền truy cập Admin | Tài khoản không đủ quyền bị từ chối | Chưa kiểm thử | |

## 7. Minh chứng thực hiện và tích hợp Git

### 7.1. Trương Mỹ Hoa Trang – Ban tổ chức

- **Nhánh Git:** `feature/organizer-events`
- **Commit:** `a2cc4fe` – `Hoan thien form them su kien va validation`
- **Pull Request:** 
- **Ảnh giao diện:** 
- **Kết quả kiểm thử:** Đã kiểm thử các trường hợp được liệt kê tại mục 3 của README.
- **Ghi chú tích hợp:** Đã push commit hoàn thiện form thêm sự kiện và kiểm tra dữ liệu lên nhánh `feature/organizer-events`. Các thay đổi bổ sung chưa commit cần được kiểm tra và lưu lại trước khi tích hợp vào nhánh chung.

### 7.2. Trần Thị Bình Minh

- **Nhánh Git:** `feature/event-discovery`
- **Commit:** Điền mã commit sau khi commit các thay đổi.
- **Pull Request:** Điền liên kết sau khi tạo Pull Request (nếu nhóm sử dụng).
- **Ảnh giao diện:** Wireframe Figma: https://www.figma.com/design/qmhpkTAKk4V1wn8RcFlxtm; cần bổ sung ảnh chụp chạy thực tế trên XAMPP.
- **Kết quả kiểm tra đã thực hiện:** Tất cả file PHP trong gói vượt qua kiểm tra cú pháp `php -l`.
- **Kết quả kiểm thử tích hợp:** Chưa xác minh được vì môi trường hiện tại không có PDO driver MySQL; cần chạy trên XAMPP với cơ sở dữ liệu `quan_ly_su_kien`.

### 7.3. Đặng Thu Thảo

- **Nhánh Git:** `feature/event-registration`
- **Commit:** `988f534`
- **Pull Request:** 
- **Ảnh giao diện:** 
- **Kết quả kiểm thử:** Đã test thành công các trường hợp: Đăng ký thành công, Đăng ký trùng, Đăng ký khi hết chỗ, Hủy đăng ký thành công, Hủy khi quá hạn. Giao diện hiển thị đúng.
- **Ghi chú tích hợp:** Đã tích hợp thành công Fetch API. Đã xử lý triệt để lỗi Race Condition khi đăng ký bằng Transaction và FOR UPDATE.

### 7.4. Nguyễn Phương Anh

- **Nhánh Git dự kiến theo phân công:** `feature/admin-clubs-users`.
- **Commit:** Chưa cung cấp mã commit; bổ sung sau khi kiểm tra lịch sử Git.
- **Pull Request:** Chưa cung cấp liên kết.
- **Ảnh giao diện:** Bổ sung ảnh Dashboard Admin, danh sách câu lạc bộ, form thêm/sửa và trang tài khoản sau khi lưu vào thư mục minh chứng.
- **Kết quả kiểm thử:** Đã kiểm tra thao tác xóa câu lạc bộ thành công và giao diện hiển thị thông báo “Xóa câu lạc bộ thành công!”. Chưa có kết quả kiểm thử xác nhận cho trường hợp xóa bị ràng buộc khóa ngoại, danh sách tài khoản và phân quyền Admin.
- **Ghi chú tích hợp:** Kiểm tra trạng thái nhánh, commit và Pull Request thực tế trước khi nộp; không ghi nhận các mục chưa xác minh là hoàn thành.

### 7.5. Bùi Mai Phương

- **Nhánh Git:** `feature/admin-events-stats`
- **Commit:** Bổ sung mã commit và nội dung commit sau khi commit thành công.
- **Pull Request:** Bổ sung liên kết Pull Request sau khi tạo; nếu chưa tạo, ghi rõ trạng thái.
- **Controller:** `app/Controllers/AdminController.php` – xử lý các chức năng quản trị thuộc phạm vi được phân công.
- **View quản lý sự kiện:** `app/Views/admin/su_kien.php`.
- **View chi tiết sự kiện:** `app/Views/admin/chi_tiet_su_kien.php`.
- **View thống kê:** `app/Views/admin/thong_ke.php`.
- **View quản lý tài khoản Ban tổ chức:** `app/Views/admin/tai_khoan.php`.
- **Checklist kiểm thử:** `docs/checklist_kiem_thu_sv5.md` – danh sách các trường hợp kiểm thử chức năng Admin; kết quả thực tế được cập nhật sau khi chạy kiểm thử.
- **Danh sách route:** `docs/routes_sv5.md` – tổng hợp các route thuộc phạm vi phụ trách và chức năng tương ứng.
- **Ghi chú tích hợp:** Các thay đổi được quản lý trên nhánh `feature/admin-events-stats`. Trước khi commit, kiểm tra danh sách file và nội dung thay đổi đã staged để bảo đảm chỉ đưa vào commit những phần thuộc phạm vi phụ trách hoặc đã được nhóm thống nhất. Các chức năng, kết quả kiểm thử và minh chứng chỉ được xác nhận hoàn thành sau khi kiểm tra thực tế.

## 8. Hướng dẫn cài đặt và chạy dự án

### 8.1. Yêu cầu môi trường

- PHP: 
- MySQL/MariaDB:
- XAMPP:
- Trình duyệt:
- Các công cụ cần thiết khác:

### 8.2. Cài đặt cơ sở dữ liệu

1. Khởi động Apache và MySQL trong XAMPP.
2. Mở phpMyAdmin.
3. Tạo cơ sở dữ liệu theo cấu hình của dự án.
4. Chạy file SQL tạo cấu trúc bảng.
5. Chạy file dữ liệu mẫu nếu có.

Tên cơ sở dữ liệu: 

Các file SQL cần chạy:

### 8.3. Cấu hình kết nối

File cấu hình kết nối cơ sở dữ liệu:

Các thông số cần cấu hình:

### 8.4. Chạy dự án

1. Đặt thư mục dự án trong thư mục `htdocs` của XAMPP.
2. Kiểm tra cấu hình kết nối cơ sở dữ liệu.
3. Khởi động Apache và MySQL.
4. Mở địa chỉ dự án trên trình duyệt.

Địa chỉ chạy dự án:

### 8.5. Tài khoản kiểm thử

| Vai trò | Tên đăng nhập/email | Mật khẩu kiểm thử |
|---|---|---|
| Admin | admin@example.com |  |
| Ban tổ chức | btc@example.com |  |
| Thành viên | thanhvien@example.com |  |

## 9. Tổng kết và tình trạng hoàn thành

### 9.1. Kết quả đã hoàn thành

- SV1: Xây dựng form thêm/sửa sự kiện và kiểm tra dữ liệu theo phần minh chứng đã ghi trong README.
- SV2: Cần cập nhật kết quả thực tế của chức năng danh sách, chi tiết, tìm kiếm và lọc sự kiện.
- SV3: Cần cập nhật kết quả thực tế của chức năng đăng ký, hủy đăng ký và Fetch API.
- SV4: Đã xây dựng các màn hình Dashboard Admin, danh sách câu lạc bộ, form thêm/sửa câu lạc bộ và danh sách tài khoản; đã kiểm tra thao tác xóa câu lạc bộ thành công. Controller tài khoản hiện mới có chức năng lấy và hiển thị danh sách; cập nhật tài khoản/vai trò, xác minh phân quyền Admin và kiểm thử tình huống xóa bị ràng buộc khóa ngoại còn cần hoàn thiện hoặc xác nhận thêm.
- SV5: Cần cập nhật kết quả thực tế của chức năng quản lý sự kiện Admin và thống kê.

### 9.2. Kết quả tích hợp toàn hệ thống

- Các nhánh đã tích hợp:
- Các chức năng đã kiểm thử:
- Các lỗi còn tồn tại:
- Các chức năng chưa hoàn thành:

### 9.3. Minh chứng nộp bài

- Repository GitHub:
- Pull Request hoặc commit cuối:
- Thư mục ảnh minh chứng:
- File slide:
- Tài liệu wireframe:
- Người tổng hợp: Bùi Mai Phương
