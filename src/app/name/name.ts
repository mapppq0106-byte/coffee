import { Component, Input, Output, EventEmitter, OnInit } from '@angular/core';

@Component({
  selector: 'app-name',
  templateUrl: './name.html',
  styleUrl: './name.css',
  standalone: false
})
export class Name implements OnInit {
  // Nhận danh sách 10 sản phẩm từ App Component (Cha)
  @Input() productList: any[] = [];
  
  // Sự kiện gửi tổng tiền ra bên ngoài cho App Component
  @Output() totalChange = new EventEmitter<number>();

  // Biến điều khiển Layout (Mặc định là true - hiển thị dạng lưới)
  isGridLayout: boolean = true;

  ngOnInit() {
    // 1. Khởi tạo trạng thái mặc định cho từng sản phẩm nhận được
    if (this.productList && this.productList.length > 0) {
      this.productList.forEach(product => {
        // Thiết lập biến thể màu sắc đầu tiên và kích thước đầu tiên làm mặc định
        product.selectedVariant = product.variants[0];
        product.selectedSizeObj = product.variants[0].sizes[0];
        product.quantity = 1;
        product.currentIndex = 1;
      });
    }
    
    // 2. TÍNH TOÁN TỔNG TIỀN LẦN ĐẦU TIÊN
    setTimeout(() => {
      this.emitTotal();
    });
  }

  // Hàm tính tổng số tiền của tất cả sản phẩm 
  emitTotal() {
    const total = this.productList.reduce((sum, product) => {
      return sum + (this.getPrice(product) * product.quantity);
    }, 0);
    this.totalChange.emit(total);
  }

  // Chuyển đổi giữa giao diện Lưới (Grid) và Danh sách (List)
  toggleLayout() {
    this.isGridLayout = !this.isGridLayout;
  }

  // Lấy giá hiện tại = Giá màu + Chênh lệch kích thước
  getPrice(product: any) {
    if (!product.selectedVariant || !product.selectedSizeObj) return 0;
    return product.selectedVariant.price + product.selectedSizeObj.priceAdjustment;
  }

  onSlide(event: any, product: any) {
    product.currentIndex = event.to + 1;
  }

  // Thay đổi số lượng sản phẩm
  updateQuantity(val: number, product: any) {
    if (product.quantity + val >= 1) {
      product.quantity += val;
      this.emitTotal(); // Cập nhật lại tổng tiền ngay lập tức
    }
  }

  // Chọn màu sắc mới cho sản phẩm
  selectColor(variant: any, product: any) {
    product.selectedVariant = variant;
    // Tìm kích thước tương đương ở màu mới để giữ lựa chọn của người dùng
    const matchingSize = variant.sizes.find((s: any) => s.size === product.selectedSizeObj.size);
    product.selectedSizeObj = matchingSize || variant.sizes[0];
    product.currentIndex = 1;
    this.emitTotal(); // Cập nhật lại tổng tiền vì giá màu có thể khác nhau
  }

  // Chọn kích thước mới cho sản phẩm
  selectSize(sizeObj: any, product: any) {
    product.selectedSizeObj = sizeObj;
    this.emitTotal(); // Cập nhật lại tổng tiền vì giá size có thể khác nhau
  }

  onBuyNow(product: any) {
    alert(`Đã thêm vào giỏ hàng!\nSản phẩm: ${product.name}\nMàu: ${product.selectedVariant.colorName}\nSize: ${product.selectedSizeObj.size}\nSố lượng: ${product.quantity}`);
  }

  onFavorite() {
    alert('Đã thêm vào yêu thích!');
  }
}
