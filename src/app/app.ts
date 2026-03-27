import { Component, signal } from '@angular/core';

@Component({
  selector: 'app-root',
  templateUrl: './app.html',
  standalone: false,
  styleUrl: './app.css'
})
export class App {
  protected readonly title = signal('demo');
  
  // Biến lưu trữ tổng tiền nhận từ component con
  totalOrderPrice: number = 0;

  // DANH SÁCH 10 SẢN PHẨM GỐC
  // Bạn có thể thay đổi link ảnh (images) và giá (price) cho từng sản phẩm tại đây
  products = Array.from({ length: 10 }, (_, i) => ({
    id: i + 1,
    name: `Bed Side Table #${i + 1}`,
    description: 'A beautiful side table that will perfectly fit your lovely bed room. With space for your books, lamps and electronic devices.',
    variants: [
      {
        colorName: 'Black',
        colorCode: '#1a1a1a',
        price: 15000,
        oldPrice: 25000,
        images: [
          'https://i.pinimg.com/1200x/be/85/b9/be85b9852b2633e58087b3df54eda5b2.jpg',
          'https://i.pinimg.com/1200x/be/85/b9/be85b9852b2633e58087b3df54eda5b2.jpg'
        ],
        sizes: [
          { size: '42*40', priceAdjustment: 0 },
          { size: '40*40', priceAdjustment: -500 },
          { size: '35*49', priceAdjustment: -1000 }
        ]
      },
      {
        colorName: 'Red',
        colorCode: '#d93025',
        price: 16500,
        oldPrice: 26500,
        images: [
          'https://i.pinimg.com/1200x/12/c8/1d/12c81d9f23f7d0c5a8fb3abb82e197e4.jpg',
          'https://i.pinimg.com/1200x/12/c8/1d/12c81d9f23f7d0c5a8fb3abb82e197e4.jpg'
        ],
        sizes: [
          { size: '42*40', priceAdjustment: 0 },
          { size: '40*40', priceAdjustment: -500 },
          { size: '35*49', priceAdjustment: -1000 }
        ]
      },
      {
        colorName: 'Orange',
        colorCode: '#f1a843',
        price: 15500,
        oldPrice: 25500,
        images: [
          'https://i.pinimg.com/1200x/10/bb/e3/10bbe375ded49332140e9ad8f18dfea8.jpg',
          'https://i.pinimg.com/1200x/10/bb/e3/10bbe375ded49332140e9ad8f18dfea8.jpg'
        ],
        sizes: [
          { size: '42*40', priceAdjustment: 0 },
          { size: '40*40', priceAdjustment: -500 },
          { size: '35*49', priceAdjustment: -1000 }
        ]
      }
    ]
  }));

  /**
   * Hàm xử lý sự kiện nhận được từ Component con (app-name).
   */
  onTotalUpdated(newTotal: number) {
    this.totalOrderPrice = newTotal;
  }
}