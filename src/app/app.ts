import { Component, signal } from '@angular/core';

@Component({
  selector: 'app-root',
  templateUrl: './app.html',
  standalone: false,
  styleUrl: './app.css'
})
export class App {
  protected readonly title = signal('slide1');
  protected readonly title1 = signal('Phan Phú Quý vs MSSV PD10948');
}
