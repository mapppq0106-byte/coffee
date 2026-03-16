import { Component } from '@angular/core';

@Component({
  selector: 'app-name',
  standalone: false,
  templateUrl: './name.html',
  styleUrl: './name.css',
})
export class Name {
  name: string = 'Người Nhện (Spider-Man)';
  myUrl: string = 'https://i.pinimg.com/736x/f6/2c/67/f62c675e9a6ee4465be8f9a68dd70b4f.jpg';
}
