# KIỂM THỬ CRUD SỰ KIỆN

## 1. Thông tin chung

- **Dự án:** Hệ thống quản lý sự kiện câu lạc bộ sinh viên
- **Chức năng kiểm thử:** Quản lý sự kiện dành cho Ban tổ chức
- **Bảng dữ liệu:** `su_kien`
- **Người thực hiện:** Trương Mỹ Hoa
- **Môi trường kiểm thử:** PHP, MySQL, PDO, XAMPP

## 2. Các trường hợp kiểm thử

### TC01 – Thêm sự kiện với dữ liệu hợp lệ

- **Mục tiêu:** Kiểm tra chức năng thêm sự kiện.
- **Dữ liệu đầu vào:** Tên sự kiện, thời gian, địa điểm, số lượng người tham gia và các thông tin bắt buộc đều hợp lệ.
- **Các bước thực hiện:**
  1. Đăng nhập bằng tài khoản Ban tổ chức.
  2. Mở trang thêm sự kiện.
  3. Nhập đầy đủ thông tin hợp lệ.
  4. Nhấn nút lưu.
- **Kết quả mong đợi:** Sự kiện được lưu vào cơ sở dữ liệu và hiển thị trong danh sách sự kiện.
- **Kết quả thực tế:** Sự kiện được thêm thành công và hiển thị trong danh sách.
- **Trạng thái:** Đạt

### TC02 – Thêm sự kiện với dữ liệu không hợp lệ

- **Mục tiêu:** Kiểm tra việc kiểm tra dữ liệu đầu vào.
- **Dữ liệu đầu vào:** Để trống tên sự kiện hoặc nhập số lượng người tham gia nhỏ hơn 0.
- **Các bước thực hiện:**
  1. Mở trang thêm sự kiện.
  2. Nhập dữ liệu không hợp lệ.
  3. Nhấn nút lưu.
- **Kết quả mong đợi:** Hệ thống thông báo lỗi, không lưu sự kiện không hợp lệ vào cơ sở dữ liệu.
- **Kết quả thực tế:** Hệ thống thông báo tên sự kiện không được để trống và không lưu dữ liệu.
- **Trạng thái:** Đạt

### TC03 – Sửa thông tin sự kiện với dữ liệu hợp lệ

- **Mục tiêu:** Kiểm tra chức năng cập nhật sự kiện.
- **Dữ liệu đầu vào:** Chọn một sự kiện đã tồn tại và thay đổi địa điểm hoặc tên sự kiện thành giá trị hợp lệ.
- **Các bước thực hiện:**
  1. Mở danh sách sự kiện.
  2. Chọn chức năng sửa của một sự kiện.
  3. Thay đổi thông tin.
  4. Nhấn nút lưu.
- **Kết quả mong đợi:** Thông tin mới được cập nhật trong cơ sở dữ liệu và hiển thị chính xác trên giao diện.
- **Kết quả thực tế:** Địa điểm sự kiện được cập nhật thành công.
- **Trạng thái:** Đạt

### TC04 – Tìm kiếm sự kiện

- **Mục tiêu:** Kiểm tra chức năng tìm kiếm sự kiện.
- **Dữ liệu đầu vào:** Nhập tên một sự kiện đã tồn tại trong cơ sở dữ liệu.
- **Các bước thực hiện:**
  1. Mở danh sách sự kiện.
  2. Nhập tên sự kiện vào ô tìm kiếm.
  3. Thực hiện tìm kiếm.
- **Kết quả mong đợi:** Hệ thống hiển thị sự kiện phù hợp với từ khóa. Nếu không có kết quả phù hợp, hệ thống thông báo hoặc hiển thị danh sách trống.
- **Kết quả thực tế:** Hệ thống hiển thị đúng sự kiện theo từ khóa đã nhập.	
- **Trạng thái:** Đạt

### TC05 – Xóa hoặc hủy sự kiện không tồn tại

- **Mục tiêu:** Kiểm tra xử lý khi thao tác với sự kiện không tồn tại.
- **Dữ liệu đầu vào:** Gửi yêu cầu xóa hoặc hủy với ID sự kiện không tồn tại trong cơ sở dữ liệu.
- **Các bước thực hiện:**
  1. Gửi yêu cầu xóa hoặc hủy sự kiện với ID không tồn tại.
  2. Kiểm tra phản hồi của hệ thống.
  3. Kiểm tra dữ liệu trong cơ sở dữ liệu.
- **Kết quả mong đợi:** Hệ thống thông báo lỗi phù hợp hoặc không thực hiện thao tác; không làm ảnh hưởng đến các sự kiện khác.
- **Kết quả thực tế:** Hệ thống thông báo không tìm thấy sự kiện và không thay đổi dữ liệu khác.
- **Trạng thái:** Đạt

## 3. Kết luận

Sau khi thực hiện kiểm thử, tổng hợp số trường hợp đạt và không đạt. Ghi nhận các lỗi phát hiện được và cập nhật chức năng nếu cần.

- Tổng số trường hợp kiểm thử: 5
- Số trường hợp đạt: 5
- Số trường hợp không đạt: 0

