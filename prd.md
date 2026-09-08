# PRD — NetFly Corporate Network Monitoring & SLA Management System

**Versi:** 1.0  
**Status:** Draft  
**Last Updated:** September 2026  
**Author:** Product Team  
**Approval:** Pending

---

## Daftar Isi

1. [Executive Summary](#1-executive-summary)
1.5. [Technology Stack Overview & Rationale](#15-technology-stack-overview--rationale)
2. [Product Overview](#2-product-overview)
3. [Problem Statement](#3-problem-statement)
4. [Goals & Objectives](#4-goals--objectives)
5. [Scope](#5-scope)
6. [Target Users](#6-target-users)
7. [Core Features](#7-core-features)
8. [Monitoring Concepts](#8-monitoring-concepts)
9. [System Architecture](#9-system-architecture)
10. [User Interface & Information Architecture](#10-user-interface--information-architecture)
11. [User Flows](#11-user-flows)
12. [Data Models](#12-data-models)
13. [Integrations](#13-integrations)
14. [Technology Stack](#14-technology-stack)
15. [Non-Functional Requirements](#15-non-functional-requirements)
16. [Acceptance Criteria](#16-acceptance-criteria)
17. [Implementation Roadmap](#17-implementation-roadmap)
18. [KPIs & Success Metrics](#18-kpis--success-metrics)
19. [Risks & Mitigation](#19-risks--mitigation)
20. [Appendix](#20-appendix)

---

## 1. Executive Summary

**NetFly Corporate Network Monitoring & SLA Management System** adalah platform monitoring terpusat berbasis web yang dirancang untuk memberikan visibilitas real-time terhadap kesehatan infrastruktur jaringan corporate customer NetFly.

Sistem ini memungkinkan Network Operations Center (NOC) NetFly untuk:
- **Monitor** kondisi koneksi customer secara real-time
- **Detect** gangguan dan anomali performa jaringan
- **Analyze** root cause dan impact terhadap SLA
- **Respond** dengan cepat terhadap incident
- **Report** performa layanan dan compliance SLA kepada stakeholder

Dengan demikian, sistem ini mendukung komitmen NetFly untuk memberikan layanan corporate dengan uptime 99%–99.5% dan manajemen SLA yang transparan.

**Product Name:** NetFly NMS (Network Management System)  
**Target Users:** NOC Team, Network Admin, Technical Support, Manager, Corporate Customer  
**Launch Timeline:** 6 bulan (3 fase)  
**Success Metric:** Reduction in MTTR (Mean Time To Resolution) sebesar 40% dan SLA compliance rate > 99.5%

---

## 1.5 Technology Stack Overview & Rationale

**Why this specific tech stack?**

This PRD adopts a **Laravel + Vue.js** architecture tailored for NetFly's monitoring requirements:

### Backend: Laravel (PHP)
- ✅ **Task Scheduling**: Built-in Scheduler for recurring ping/SNMP tasks (no external cron needed)
- ✅ **Database ORM**: Eloquent makes MySQL queries simple and type-safe
- ✅ **Real-time Ready**: Native support for WebSocket broadcasting via Laravel Reverb
- ✅ **Queue System**: Built-in job queue for async notifications and report generation
- ✅ **Security**: Sanctum provides token-based API authentication
- ✅ **Developer Experience**: Laravel's conventions reduce development time
- ✅ **Ecosystem**: Mature package ecosystem (spatie/permission, query-builder, etc.)

### Frontend: Vue.js 3 + Tailwind CSS
- ✅ **Real-time UI**: Vue's reactivity perfect for live dashboard updates
- ✅ **Lightweight**: Vue is smaller than React (good for performance)
- ✅ **Styling**: Tailwind CSS provides rapid UI development without writing custom CSS
- ✅ **Responsive**: Tailwind's mobile-first approach ensures responsiveness
- ✅ **Developer Experience**: Simple syntax, easy to learn for team
- ✅ **Socket.IO Integration**: Works seamlessly with WebSocket events from Laravel Reverb

### Real-time Communication: Laravel Reverb + Socket.IO
- ✅ **No External Service**: Reverb runs on your servers (not third-party SaaS)
- ✅ **Cost-effective**: No per-user charges like Firebase or Pusher
- ✅ **Native Laravel**: Built by Laravel team, first-class integration
- ✅ **Scalable**: Can handle thousands of concurrent connections
- ✅ **Use Case Fit**: Perfect for dashboard status push without page refresh

### Database: MySQL
- ✅ **Scalable**: Table partitioning for large metric datasets
- ✅ **Reliable**: Proven production database
- ✅ **Cost**: Free, open-source
- ✅ **Integration**: First-class Laravel support
- ✅ **Metrics Storage**: With partitioning, can store millions of data points efficiently
- ✅ **Querying**: MySQL 8.0+ supports window functions for analytics

### Monitoring Workers: Laravel Scheduler + Supervisor
- ✅ **Reliability**: Supervisor restarts failed processes automatically
- ✅ **Simplicity**: No need for separate worker services (everything in Laravel)
- ✅ **Scheduling**: Laravel Scheduler handles cron expressions elegantly
- ✅ **Monitoring**: Built-in command monitoring and error handling

---

## 2. Product Overview

### 2.1 Background

NetFly menyediakan layanan jaringan corporate dengan berbagai solusi konektivitas:
- **Internet Fiber Optic** (dedicated, 1:1 ratio, guaranteed bandwidth)
- **Internet Wireless** (mobile & broadband)
- **VPN Solutions** (site-to-site, remote access)
- **VoIP Services** (IP-based telephony)
- **IT Solutions** (managed services)

Layanan corporate NetFly ditujukan untuk mendukung operasional bisnis customer yang membutuhkan:
- **High Reliability**: Jaringan yang stabil untuk mission-critical applications (ERP, CRM, database, SAP)
- **Dedicated Resources**: Bandwidth dedicated dengan rasio 1:1
- **Service Level Agreement**: Uptime guarantee hingga 99.5%
- **24/7 Support**: Technical support sepanjang waktu

### 2.2 Current Challenge

Seiring bertambahnya jumlah corporate customer, monitoring jaringan menjadi kompleks:
- ❌ **Visibility Gap**: NOC tidak memiliki single dashboard untuk melihat status semua customer
- ❌ **Reactive Response**: Gangguan hanya terdeteksi setelah customer komplain
- ❌ **Manual SLA Tracking**: Perhitungan uptime/downtime masih manual dan tidak real-time
- ❌ **Slow Incident Detection**: Time-to-detect gangguan masih lambat, menyebabkan SLA breach
- ❌ **Limited Historical Data**: Tidak ada insight mendalam tentang performa trend customer
- ❌ **No Root Cause Analysis**: Sulit mengidentifikasi apakah masalah di link, device, atau konfigurasi

### 2.3 Solution Overview

Sistem monitoring terpusat yang menyediakan:
1. **Real-time Visibility** — Dashboard terpadu untuk monitoring status customer
2. **Proactive Monitoring** — Alert system yang mendeteksi masalah sebelum SLA breach
3. **Automated SLA Calculation** — Real-time SLA compliance tracking
4. **Incident Management** — Workflow untuk deteksi, investigasi, dan resolusi incident
5. **Performance Analytics** — Historical data dan trend analysis
6. **Comprehensive Reporting** — Reports untuk NOC, Manager, dan Customer

---

## 3. Problem Statement

### Pertanyaan yang Harus Dijawab oleh Sistem

Tim NetFly membutuhkan jawaban cepat untuk pertanyaan-pertanyaan berikut:

| # | Pertanyaan | Current State | Desired State |
|---|-----------|---------------|---------------|
| 1 | Berapa banyak customer yang online sekarang? | Manual check | Real-time dashboard |
| 2 | Customer mana yang mengalami gangguan? | Manual escalation | Automated detection |
| 3 | Seberapa baik performa bandwidth customer? | Check per request | Real-time graph |
| 4 | Berapa latency jaringan customer? | Ping manual | Continuous monitoring |
| 5 | Apakah ada packet loss? | Manual test | Automated detection |
| 6 | Berapa uptime layanan customer? | Manual calculation | Auto-calculated |
| 7 | Apakah SLA customer terpenuhi? | Manual tracking | Real-time compliance status |
| 8 | Apa root cause dari gangguan? | Manual investigation | Correlated metrics & analysis |

### Business Impact

- **SLA Breach Risk**: Keterlambatan deteksi gangguan menyebabkan downtime tidak tertangani dengan cepat
- **Operational Inefficiency**: NOC harus manually check status customer, tidak efisien
- **Customer Dissatisfaction**: Customer corporate tidak punya visibility terhadap performa layanan mereka
- **Revenue Risk**: SLA breach dapat menghasilkan penalty atau churn customer
- **Decision Making**: Lack of data membuat sulit untuk identify trend dan plan capacity

---

## 4. Goals & Objectives

### 4.1 Primary Goal

**Membangun platform monitoring terpusat yang memberdayakan NOC untuk mengelola kesehatan jaringan corporate customer dengan transparansi, kecepatan, dan akurasi tinggi.**

### 4.2 Strategic Objectives

1. **Reduce Mean Time To Resolution (MTTR)**
   - Target: Turun 40% (dari rata-rata 45 menit → 27 menit)
   - Measurement: Tracking incident resolution time

2. **Improve SLA Compliance Rate**
   - Target: > 99.5% SLA met rate
   - Measurement: Actual uptime vs target SLA per customer

3. **Enhance Operational Efficiency**
   - Target: Reduce manual monitoring tasks sebesar 80%
   - Measurement: Hours saved per week on manual checks

4. **Increase Transparency with Customers**
   - Target: Enable customer self-service SLA dashboard
   - Measurement: Customer adoption rate of portal

5. **Enable Proactive Management**
   - Target: Detect 90% of issues before SLA breach
   - Measurement: Alert accuracy rate (true positive vs false positive)

### 4.3 Success Criteria

- ✅ Dashboard operational dengan real-time update (< 1 menit delay)
- ✅ Alert system mendeteksi 90%+ incident sebelum customer notice
- ✅ SLA calculation akurat dan automated
- ✅ NOC dapat resolve 95%+ incident dalam dashboard (tanpa perlu external tools)
- ✅ Reporting automated dan tersedia on-demand
- ✅ System availability > 99.9% (SLA 99.9% uptime)
- ✅ Response time < 2 detik untuk dashboard load
- ✅ Customer adoption rate > 70% dalam 3 bulan

---

## 5. Scope

### 5.1 In Scope (MVP + Phase 1-2)

**Core Modules:**

| Module | Features | Priority |
|--------|----------|----------|
| **Dashboard** | Real-time KPI, Network Health, Customer Status, Active Incidents | P0 |
| **Customer Management** | Add/Edit/View Corporate Customers, Service List | P0 |
| **Service Management** | Manage services per customer (Fiber, VPN, VoIP, Wireless) | P0 |
| **Network Monitoring** | Device status, Connection status, Interface monitoring | P0 |
| **Bandwidth Monitoring** | Upload/Download tracking, Utilization graph, Capacity planning | P0 |
| **Quality Monitoring** | Latency, Packet loss, Jitter tracking | P0 |
| **Availability Monitoring** | Uptime/Downtime calculation, Historical tracking | P0 |
| **SLA Monitoring** | SLA compliance status, At-risk detection, SLA breach alert | P0 |
| **Incident Management** | Incident creation, Status workflow, Assignment, Timeline | P0 |
| **Alert Management** | Threshold-based alerts, Alert escalation, Alert suppression | P0 |
| **Network Topology** | Visual representation of network architecture per customer | P1 |
| **Reporting** | Performance report, SLA report, Incident report, Export to PDF | P1 |
| **User Management** | Role-based access control, User provisioning, Audit log | P0 |
| **Notification** | Email, Dashboard notification, SMS (optional) | P0 |
| **Settings & Configuration** | Alert thresholds, SLA targets, Notification preferences | P0 |

**Integration:**

| Integration | Purpose | Priority |
|-------------|---------|----------|
| **Network Devices (SNMP)** | Collect metrics from routers, switches, modem | P0 |
| **Ping/Traceroute** | Connectivity and latency monitoring | P0 |
| **NetFlow/sFlow** | Bandwidth utilization tracking | P1 |
| **Syslog** | Device event collection | P1 |
| **Email Service** | Notification delivery | P0 |
| **NMS Existing Tools** | Integration dengan monitoring tools existing (jika ada) | P1 |

### 5.2 Out of Scope (Future/Phase 3+)

- ❌ Billing & Invoicing
- ❌ CRM & Sales Management
- ❌ Inventory Management
- ❌ Procurement
- ❌ Accounting & Finance
- ❌ HR & Payroll
- ❌ Ticketing System (terintegrasi minimal saja)
- ❌ AI-powered anomaly detection (Phase 2+)
- ❌ Advanced predictive analytics
- ❌ Mobile app (Web responsive hanya)

---

## 6. Target Users

### 6.1 User Personas

#### Persona 1: NOC Operator (Primary User)

**Name:** Budi, NOC Operator  
**Role:** Network Operations Center  
**Goals:**
- Monitor kesehatan jaringan customer secara real-time
- Quickly respond to incidents dan minimize downtime
- Generate daily/weekly reports untuk management

**Pain Points:**
- Terlalu banyak tools untuk monitor satu customer
- Manual check untuk setiap customer adalah time-consuming
- Tidak ada unified view untuk identify trends

**Expected Usage:** 8 jam/hari, multiple dashboards open

---

#### Persona 2: Network Administrator

**Name:** Rina, Network Admin  
**Role:** Infrastructure Team Lead  
**Goals:**
- Manage network devices dan services
- Configure monitoring parameters dan thresholds
- Analyze trend dan plan capacity

**Pain Points:**
- Need visibility across multiple customer networks
- Difficulty in root cause analysis
- Manual device management

**Expected Usage:** 6 jam/hari, configuration + monitoring

---

#### Persona 3: Technical Support

**Name:** Ahmad, Support Engineer  
**Role:** Customer Support Team  
**Goals:**
- Quickly diagnose customer issues
- Provide accurate information about service status
- Track incident resolution

**Pain Points:**
- Customer calls without full context
- No direct access to detailed metrics
- Manual escalation process

**Expected Usage:** 4 jam/hari, incident handling + status check

---

#### Persona 4: Manager/Supervisor

**Name:** Hendra, NOC Manager  
**Role:** Operations Management  
**Goals:**
- Monitor team performance
- Track SLA compliance across all customers
- Generate management reports

**Pain Points:**
- Time spent on manual reporting
- No visibility on KPIs
- Difficulty in identifying root causes

**Expected Usage:** 2 jam/hari, dashboard + reports

---

#### Persona 5: Corporate Customer

**Name:** Bambang, IT Manager @ PT ABC  
**Role:** Customer IT Department  
**Goals:**
- Monitor own service status
- Check SLA compliance
- Export performance reports

**Pain Points:**
- No visibility on own service
- Manual reporting from NetFly support
- Reactive notification (after incident)

**Expected Usage:** 1-2 jam/hari, self-service dashboard

---

### 6.2 User Roles & Permissions

| Feature/Action | Super Admin | Network Admin | NOC Operator | Support | Manager | Customer |
|---|:---:|:---:|:---:|:---:|:---:|:---:|
| **Dashboard** | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| View All Customers | ✓ | ✓ | ✓ | ✓ | ✓ | - |
| View Own Service Only | - | - | - | - | - | ✓ |
| Add/Edit Customer | ✓ | ✓ | - | - | - | - |
| Add/Edit Service | ✓ | ✓ | - | - | - | - |
| View Network Metrics | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Create Incident | ✓ | ✓ | ✓ | ✓ | - | - |
| Acknowledge Incident | ✓ | ✓ | ✓ | ✓ | - | - |
| Resolve Incident | ✓ | ✓ | ✓ | ✓ | - | - |
| Configure Alert Threshold | ✓ | ✓ | - | - | - | - |
| Configure SLA Target | ✓ | ✓ | - | - | - | - |
| View SLA Report | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Export Report | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Manage Users | ✓ | - | - | - | - | - |
| Access Audit Log | ✓ | ✓ | - | - | - | - |
| System Settings | ✓ | - | - | - | - | - |

---

## 7. Core Features

### 7.1 Feature Overview

```
NetFly Network Monitoring System
├── 📊 Dashboard Module
│   ├── System Health Overview
│   ├── KPI Summary
│   ├── Active Incidents
│   ├── SLA Compliance View
│   └── Quick Actions
│
├── 👥 Customer Management
│   ├── Customer List/Detail
│   ├── Service Assignment
│   ├── Contact Management
│   └── Service Configuration
│
├── 🔗 Network Monitoring
│   ├── Device Status Monitoring
│   ├── Connection Status
│   ├── Interface Monitoring
│   ├── Real-time Metrics
│   └── Topology View
│
├── 📊 Performance Monitoring
│   ├── Bandwidth Tracking
│   ├── Latency Monitoring
│   ├── Packet Loss Detection
│   ├── Jitter Tracking
│   └── Performance Graphs
│
├── ⏱️ Availability Monitoring
│   ├── Uptime Calculation
│   ├── Downtime Tracking
│   ├── Historical Data
│   └── Availability Graph
│
├── 📋 SLA Management
│   ├── SLA Configuration
│   ├── Compliance Tracking
│   ├── At-Risk Detection
│   ├── Breach Alert
│   └── SLA Report
│
├── 🚨 Incident Management
│   ├── Automated Incident Creation
│   ├── Incident Status Workflow
│   ├── Assignment & Escalation
│   ├── Root Cause Analysis
│   ├── Timeline Tracking
│   └── Incident History
│
├── ⚠️ Alert Management
│   ├── Threshold Configuration
│   ├── Alert Generation
│   ├── Alert Escalation
│   ├── Alert Suppression
│   └── Alert History
│
├── 📈 Reporting
│   ├── Performance Report
│   ├── SLA Compliance Report
│   ├── Incident Report
│   ├── Custom Report Builder
│   └── Scheduled Report
│
├── 🔐 User Management
│   ├── User Provisioning
│   ├── Role-Based Access
│   ├── Permission Management
│   ├── Audit Logging
│   └── Session Management
│
└── ⚙️ System Configuration
    ├── Alert Thresholds
    ├── SLA Templates
    ├── Notification Rules
    ├── Integration Settings
    └── System Preferences
```

---

## 8. Monitoring Concepts

### 8.1 The 8 Key Monitoring Questions

Sistem dirancang untuk menjawab 8 pertanyaan kritis:

#### ❶ Apakah customer online?
**Metric:** Connection Status  
**Possible Values:** Online, Offline, Warning, Unknown  
**Update Frequency:** Real-time (every 60 seconds)  
**Alert Trigger:** Transition to Offline  

```
Status Indicator:
🟢 Online    — Connection established & healthy
🟡 Warning   — Connection unstable/degrading
🔴 Offline   — Connection down
⚫ Unknown   — No data received
```

---

#### ❷ Apakah bandwidth normal?
**Metrics:**
- Bandwidth Capacity (configured)
- Current Download
- Current Upload
- Utilization Percentage

**Update Frequency:** Every 30 seconds  
**Alert Triggers:**
- Utilization > 80% → Warning
- Utilization > 90% → Critical
- Sustained high utilization → Capacity planning alert

```
Example:
┌─────────────────────────────────────┐
│ Bandwidth Usage                     │
├─────────────────────────────────────┤
│ Capacity:     100 Mbps              │
│ Download:     72 Mbps               │
│ Upload:       68 Mbps               │
│ Utilization:  72%                   │
│ Status:       🟡 Warning (≥80%)     │
└─────────────────────────────────────┘
```

---

#### ❸ Apakah jaringan cepat?
**Metric:** Latency (Round Trip Time)  
**Unit:** milliseconds (ms)  
**Update Frequency:** Every 30 seconds  
**Typical Values:** 
- Excellent: < 20 ms
- Good: 20–50 ms
- Degraded: 50–100 ms
- Poor: > 100 ms

**Alert Triggers:**
- Average Latency > 50 ms → Warning
- Average Latency > 100 ms → Critical
- Spike (>150% increase) → Anomaly alert

```
Example:
┌─────────────────────────────────────┐
│ Network Latency                     │
├─────────────────────────────────────┤
│ Current:      18 ms     🟢 Good      │
│ Average:      16 ms                 │
│ Min:          12 ms                 │
│ Max:          42 ms                 │
│ Trend:        ↘ Improving           │
└─────────────────────────────────────┘
```

---

#### ❹ Apakah koneksi stabil?
**Metric:** Packet Loss Rate  
**Unit:** percentage (%)  
**Update Frequency:** Every 30 seconds  
**Typical Values:**
- Excellent: < 0.1%
- Good: 0.1–0.5%
- Degraded: 0.5–2%
- Poor: > 2%

**Alert Triggers:**
- Packet Loss > 0.5% → Warning
- Packet Loss > 2% → Critical
- Sudden increase → Anomaly alert

```
Example:
┌─────────────────────────────────────┐
│ Packet Loss                         │
├─────────────────────────────────────┤
│ Current:      0.2%      🟢 Good     │
│ Average:      0.15%                 │
│ Min:          0%                    │
│ Max:          2.1%                  │
│ Incidents:    2 (this hour)         │
└─────────────────────────────────────┘
```

---

#### ❺ Seberapa tersedia layanan?
**Metric:** Availability / Uptime  
**Unit:** percentage (%)  
**Calculation:** (Total Time - Downtime) / Total Time × 100%  
**Update Frequency:** Real-time (updated on state change)  

**Standard Availability Targets:**
- 99.0% = max 7.2 hours/month downtime
- 99.5% = max 3.6 hours/month downtime
- 99.9% = max 43.2 minutes/month downtime

```
Example:
┌─────────────────────────────────────┐
│ Service Availability (30 days)      │
├─────────────────────────────────────┤
│ Target:       99.5%                 │
│ Actual:       99.72%    ✓ SLA Met   │
│ Uptime:       29d 21h 16m           │
│ Downtime:     2h 14m                │
└─────────────────────────────────────┘
```

---

#### ❻ Berapa lama gangguan terjadi?
**Metric:** Downtime Duration  
**Unit:** hours, minutes, seconds  
**Tracking:** Per incident & aggregated  
**Update Frequency:** Real-time during downtime  

```
Example:
┌─────────────────────────────────────┐
│ Downtime This Month                 │
├─────────────────────────────────────┤
│ Total Downtime:  2h 14m             │
│ Incidents:       3                  │
│                                     │
│ Breakdown:                          │
│ Sep 2 @ 01:23    12m  Link Down    │
│ Sep 5 @ 03:42    28m  Device Reboot│
│ Sep 7 @ 11:10    1h 34m Connection │
└─────────────────────────────────────┘
```

---

#### ❼ Apakah SLA terpenuhi?
**Metric:** SLA Compliance Status  
**Comparison:** Actual Availability vs SLA Target  
**Status:**
- ✓ **SLA Met** — Actual ≥ Target
- ⚠ **At Risk** — Actual < Target but still acceptable (within 0.1%)
- ✗ **SLA Breached** — Actual < Target significantly

```
Example:
┌─────────────────────────────────────┐
│ SLA Compliance                      │
├─────────────────────────────────────┤
│ SLA Target:       99.5%             │
│ Actual:           99.72%            │
│ Margin:           +0.22%            │
│ Status:           ✓ SLA MET         │
│ Days Remaining:   30 (full month)   │
└─────────────────────────────────────┘

Status At Risk:
┌─────────────────────────────────────┐
│ SLA Target:       99.5%             │
│ Actual:           99.51%            │
│ Margin:           +0.01%            │
│ Status:           ⚠ AT RISK         │
│ Estimated Breach: In 2 more hours   │
└─────────────────────────────────────┘

Status Breached:
┌─────────────────────────────────────┐
│ SLA Target:       99.5%             │
│ Actual:           98.94%            │
│ Margin:           -0.56%            │
│ Status:           ✗ BREACHED        │
│ Penalty Rate:     Check Contract    │
└─────────────────────────────────────┘
```

---

#### ❽ Apakah sedang terjadi incident?
**Metric:** Active Incident Count & Details  
**Severity Levels:**
- 🔴 **Critical** — Service offline / SLA breach imminent
- 🟡 **High** — Significant performance degradation
- 🟠 **Medium** — Noticeable issue but service still running
- 🔵 **Low** — Minor issue, no impact on service

**Update Frequency:** Real-time  
**Auto-escalation:** After 15 minutes unresolved → escalate

```
Example:
┌──────────────────────────────────────┐
│ Active Incidents                     │
├──────────────────────────────────────┤
│ INC-2026-0091                        │
│ Customer:  PT ABC Indonesia          │
│ Service:   Fiber Internet Jakarta    │
│ Issue:     Connection Offline        │
│ Severity:  🔴 Critical               │
│ Detected:  14:23 (17 minutes ago)    │
│ Status:    Investigating             │
│ Assigned:  Technician: Budi Santoso  │
│ Actions:   Check link status, contact ISP
└──────────────────────────────────────┘
```

---

### 8.2 Monitoring Hierarchy

```
NetFly Infrastructure
  └─ POP Jakarta
      └─ Device: Router01 (Status: 🟢 Online)
          ├─ Interface: GigabitEthernet0/0/0 (Status: 🟢 Up)
          │   └─ Metrics:
          │       ├─ Bandwidth In: 45 Mbps
          │       ├─ Bandwidth Out: 38 Mbps
          │       ├─ Latency: 18 ms
          │       └─ Packet Loss: 0.2%
          └─ Interface: GigabitEthernet0/0/1 (Status: 🟢 Up)
              └─ Metrics:
                  ├─ Bandwidth In: 52 Mbps
                  ├─ Bandwidth Out: 48 Mbps
                  ├─ Latency: 22 ms
                  └─ Packet Loss: 0.1%

  └─ Customer: PT ABC Indonesia
      └─ Service: Fiber Internet
          └─ Device: CPE Router (Status: 🟢 Online)
              └─ Metrics:
                  ├─ Availability: 99.72%
                  ├─ SLA: 99.5% ✓ Met
                  ├─ Incidents (month): 3
                  ├─ Downtime: 2h 14m
                  ├─ Latency: 18 ms
                  ├─ Packet Loss: 0.2%
                  └─ Bandwidth: 72 Mbps / 100 Mbps (72%)
```

---

## 9. System Architecture

### 9.1 High-Level Architecture

```
┌────────────────────────────────────────────────────────┐
│                    CLIENT LAYER                        │
│  ┌─────────────────────────────────────────────────┐  │
│  │  Web UI (Vue.js 3 + Tailwind CSS)               │  │
│  │  • Dashboard                                    │  │
│  │  • Customer Management                          │  │
│  │  • Network Monitoring                           │  │
│  │  • Incident Management                          │  │
│  │  • Reporting & Analytics                        │  │
│  │  • Socket.IO for real-time updates              │  │
│  └─────────────────────────────────────────────────┘  │
└────────────────────────────────────────────────────────┘
                    ↕ REST API + WebSocket
┌────────────────────────────────────────────────────────┐
│              APPLICATION LAYER (Nginx/PHP-FPM)         │
│  ┌─────────────────────────────────────────────────┐  │
│  │  Laravel 11.x RESTful API                       │  │
│  │  • User Management & Auth (Sanctum)             │  │
│  │  • Customer & Service Management                │  │
│  │  • Dashboard Service & Endpoints                │  │
│  │  • Monitoring Service                           │  │
│  │  • Alert & Notification Service                 │  │
│  │  • SLA Calculation Service                      │  │
│  │  • Incident Management Service                  │  │
│  │  • Report Generation Service                    │  │
│  │  • Configuration Service                        │  │
│  │  • Laravel Reverb (WebSocket Layer)             │  │
│  └─────────────────────────────────────────────────┘  │
└────────────────────────────────────────────────────────┘
                          ↕
          Database / Cache / Queue Layer
┌────────────────────────────────────────────────────────┐
│                   DATA LAYER                           │
│  ┌──────────────────────────────────────────────────┐ │
│  │ MySQL              Redis         Laravel Queue   │ │
│  │ • User Data        • Cache      • Async Jobs    │ │
│  │ • Customer Data    • Sessions   • Email Queue   │ │
│  │ • Service Data     • Hot Data   • Report Gen.   │ │
│  │ • Incident Data    • Metrics                    │ │
│  │ • Alert Config     • Broadcast                  │ │
│  │ • Historical Data  • Rate Limit                 │ │
│  └──────────────────────────────────────────────────┘ │
└────────────────────────────────────────────────────────┘
                          ↕
┌────────────────────────────────────────────────────────┐
│            MONITORING WORKERS LAYER                    │
│  ┌──────────────────────────────────────────────────┐ │
│  │  Laravel Scheduler + Supervisor                  │ │
│  │  • Ping Monitor (every 30 seconds)               │ │
│  │  • SNMP Collector (every 30 seconds)             │ │
│  │  • SLA Calculator (every minute)                 │ │
│  │  • Alert Checker (every minute)                  │ │
│  │  • Queue Worker (async job processing)           │ │
│  │  • Reverb WebSocket (push to clients)            │ │
│  └──────────────────────────────────────────────────┘ │
└────────────────────────────────────────────────────────┘
                          ↕
┌────────────────────────────────────────────────────────┐
│                 INTEGRATION LAYER                      │
│  ┌──────────────────────────────────────────────────┐ │
│  │  Network Device Collectors                       │ │
│  │  • Ping/ICMP Monitor (connectivity checks)       │ │
│  │  • SNMP Collector (device metrics)               │ │
│  │  • Syslog Receiver (device events) - Phase 2     │ │
│  │  • NetFlow Collector (traffic) - Phase 2         │ │
│  │  • External API Integrations                     │ │
│  └──────────────────────────────────────────────────┘ │
└────────────────────────────────────────────────────────┘
                          ↕
┌────────────────────────────────────────────────────────┐
│              EXTERNAL DATA SOURCES                     │
│  ┌──────────────────────────────────────────────────┐ │
│  │ Network Devices                                  │ │
│  │ • Customer CPE (Routers)                         │ │
│  │ • Network Switches                               │ │
│  │ • Firewalls                                      │ │
│  │ • ISP Equipment                                  │ │
│  └──────────────────────────────────────────────────┘ │
└────────────────────────────────────────────────────────┘
```

### 9.2 Data Flow

```
DATA COLLECTION FLOW:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Network Device (Router/CPE)
    ↓ Ping/ICMP or SNMP Poll (every 30 sec)
Laravel Scheduler Command
    ├─ php artisan monitor:ping-devices
    └─ php artisan monitor:collect-snmp
    ↓ Parse & Validate Metrics
MySQL Database
    ├─ Store device_metrics record
    └─ device_metrics table (partitioned by date)
    ↓
Redis Cache
    ├─ Cache current status (key: device:123:status)
    └─ TTL: 60 seconds
    ↓
Laravel Reverb Broadcast
    ├─ Event: DeviceStatusChanged
    ├─ Channel: status.device.123
    └─ All connected Vue clients receive update
    ↓
Vue.js Socket.IO Client
    ├─ Receive WebSocket event
    ├─ Update Pinia store
    └─ Re-render dashboard component (instant)


ALERT & INCIDENT FLOW:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

New Metric Received (every 30 sec)
    ↓
Laravel Scheduler → monitor:check-alerts command
    ↓ Metric > Threshold?
    ├─ NO: Continue monitoring
    ├─ YES: Alert triggered
Alert Generated (in MySQL alerts table)
    ↓ Create/Update Incident?
    ├─ Check if incident already exists (within 5 min window)
    ├─ If exists: Link to existing incident
    └─ If not: Create new incident record
    ↓
Dispatch Alert Notification Job
    ├─ Queue job in Redis/Database
    └─ Queue worker processes immediately
    ↓
Alert Service
    ├─ Create Incident (if not exists)
    ├─ Update SLA Status
    ├─ Broadcast incident.created event via Reverb
    ├─ Send Notifications
    │   ├─ Email (via queue)
    │   ├─ Dashboard notification (WebSocket push)
    │   └─ SMS (optional, via queue)
    └─ Set escalation timer (15 min unresolved)
    ↓
Laravel Reverb Broadcasting
    ├─ Event: IncidentCreated / AlertTriggered
    ├─ Channel: incident.critical
    └─ All connected NOC dashboards get real-time update


SLA CALCULATION FLOW:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Device Status Polling (every 30 sec)
    ├─ Online: Status recorded as 1
    └─ Offline: Status recorded as 0
    ↓
Laravel Scheduler → monitor:calculate-sla (every minute)
    ↓ Query past 30 seconds of status records
    ├─ Count online seconds
    ├─ Count offline seconds
    └─ Calculate minute-level availability
    ↓
Update Service Record
    ├─ Calculate cumulative availability (month-to-date)
    ├─ current_availability_percent = (uptime_seconds / total_seconds) * 100
    ├─ Compare vs sla_target
    └─ Determine status: Met / At Risk / Breached
    ↓
Store SLA History
    ├─ Create/update sla_compliance record (daily)
    ├─ Store target_availability, actual_availability
    └─ Record any SLA breaches
    ↓
Broadcast SLA Update via Reverb
    ├─ Event: SLAUpdated
    ├─ Include: customer_id, service_id, status
    └─ Dashboard receives update
    ↓
Dashboard Displays
    ├─ SLA Status (Met ✓ / At Risk ⚠ / Breached ✗)
    ├─ Current Availability %
    ├─ Remaining Downtime Budget
    └─ Days remaining in billing period
    ↓
Report Generation (Daily/Weekly/Monthly)
    ├─ Laravel: php artisan report:generate-sla
    └─ Query sla_compliance records from past period
```

---

## 10. User Interface & Information Architecture

### 10.1 Page Structure

```
LOGIN PAGE
    ↓
DASHBOARD (Home)
    ├─ System Overview
    ├─ KPI Summary
    ├─ Active Incidents
    └─ Quick Actions

MAIN NAVIGATION
├─ Dashboard
│   ├─ Overview
│   ├─ Network Health
│   └─ Quick Stats
│
├─ Monitoring
│   ├─ Network Monitoring
│   │   ├─ Device Status
│   │   ├─ Connection Status
│   │   └─ Interface Status
│   │
│   ├─ Performance
│   │   ├─ Bandwidth
│   │   ├─ Latency & Packet Loss
│   │   └─ Performance Graphs
│   │
│   ├─ Availability
│   │   ├─ Uptime Overview
│   │   ├─ Downtime History
│   │   └─ Availability Trend
│   │
│   ├─ Topology
│   │   ├─ Network Diagram
│   │   ├─ Device Relationships
│   │   └─ Link Status
│   │
│   └─ Network Events
│       ├─ Log View
│       └─ Event Timeline
│
├─ Management
│   ├─ Customer
│   │   ├─ Customer List
│   │   ├─ Customer Detail
│   │   ├─ Service List
│   │   └─ Add/Edit Customer
│   │
│   ├─ Services
│   │   ├─ Service Inventory
│   │   ├─ Service Detail
│   │   └─ Service Configuration
│   │
│   └─ Devices
│       ├─ Device List
│       ├─ Device Detail
│       └─ Device Configuration
│
├─ Operations
│   ├─ Incidents
│   │   ├─ Incident Dashboard
│   │   ├─ Incident List
│   │   ├─ Incident Detail
│   │   └─ Create Incident
│   │
│   ├─ Alerts
│   │   ├─ Active Alerts
│   │   ├─ Alert History
│   │   ├─ Alert Configuration
│   │   └─ Alert Threshold Rules
│   │
│   └─ Maintenance
│       ├─ Scheduled Maintenance
│       ├─ Maintenance Calendar
│       └─ Maintenance History
│
├─ SLA
│   ├─ SLA Overview
│   │   └─ Compliance Dashboard
│   │
│   ├─ SLA Management
│   │   ├─ SLA Templates
│   │   ├─ Customer SLA Config
│   │   └─ SLA Targets
│   │
│   └─ SLA Reports
│       ├─ Compliance Report
│       ├─ Breach Analysis
│       └─ Trend Analysis
│
├─ Reporting
│   ├─ Performance Report
│   │   ├─ Monthly Report
│   │   ├─ Weekly Report
│   │   └─ Custom Period
│   │
│   ├─ Incident Report
│   │   ├─ Incident Summary
│   │   ├─ MTTR Analysis
│   │   └─ Severity Distribution
│   │
│   ├─ SLA Report
│   │   ├─ SLA Compliance
│   │   ├─ Breach Analysis
│   │   └─ Trend Forecast
│   │
│   └─ Report Templates
│       ├─ Custom Report
│       ├─ Scheduled Reports
│       └─ Report Export
│
├─ Configuration
│   ├─ Alert Settings
│   │   ├─ Threshold Configuration
│   │   ├─ Alert Rules
│   │   └─ Escalation Policies
│   │
│   ├─ Notifications
│   │   ├─ Notification Rules
│   │   ├─ Contact List
│   │   └─ Channel Configuration
│   │
│   ├─ Integration
│   │   ├─ SNMP Configuration
│   │   ├─ API Keys
│   │   └─ External System Sync
│   │
│   └─ System Settings
│       ├─ Preferences
│       └─ Backup & Recovery
│
├─ Administration
│   ├─ User Management
│   │   ├─ User List
│   │   ├─ Create/Edit User
│   │   ├─ Role Management
│   │   └─ Permission Management
│   │
│   ├─ Audit Log
│   │   ├─ User Activity Log
│   │   ├─ System Changes Log
│   │   └─ Login History
│   │
│   └─ System Health
│       ├─ API Health
│       ├─ Database Status
│       └─ Service Status
│
└─ Help & Support
    ├─ Documentation
    ├─ FAQ
    ├─ Support Contact
    └─ System Status Page
```

### 10.2 Dashboard Design

#### Main Dashboard Layout

```
┌─────────────────────────────────────────────────────────────┐
│ NetFly Network Monitoring | Welcome, Budi          🔔 🔧 👤 │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│ ┌─────────────────────────────────────────────────────────┐ │
│ │ QUICK STATS                                             │ │
│ ├─────────────────────────────────────────────────────────┤ │
│ │                                                         │ │
│ │  Total Customers   Online   Offline   Warning   Critical│ │
│ │       128            121       3         3        1     │ │
│ │                                                         │ │
│ └─────────────────────────────────────────────────────────┘ │
│                                                             │
│ ┌──────────────────────────────────┬──────────────────────┐ │
│ │ NETWORK HEALTH                   │ SLA COMPLIANCE       │ │
│ ├──────────────────────────────────┼──────────────────────┤ │
│ │                                  │                      │ │
│ │         94%                      │        99.72%        │ │
│ │      ↗ Improved                  │    ✓ 98% of SLA Met  │ │
│ │                                  │                      │ │
│ └──────────────────────────────────┴──────────────────────┘ │
│                                                             │
│ ┌─────────────────────────────────────────────────────────┐ │
│ │ BANDWIDTH UTILIZATION (Average across all customers)    │ │
│ ├─────────────────────────────────────────────────────────┤ │
│ │ ████████████████████░░░░░░░░░ 68%                      │ │
│ │ Peak: 92% | Average: 68% | Min: 12%                    │ │
│ └─────────────────────────────────────────────────────────┘ │
│                                                             │
│ ┌─────────────────────┬──────────────────────────────────┐ │
│ │ AVG LATENCY         │ PACKET LOSS                     │ │
│ ├─────────────────────┼──────────────────────────────────┤ │
│ │     18 ms           │          0.2%                   │ │
│ │   🟢 Excellent      │      🟢 Excellent               │ │
│ └─────────────────────┴──────────────────────────────────┘ │
│                                                             │
│ ┌─────────────────────────────────────────────────────────┐ │
│ │ ACTIVE INCIDENTS                                        │ │
│ ├─────────────────────────────────────────────────────────┤ │
│ │ 🔴 2 Critical   │ 🟡 3 High   │ 🟠 1 Medium              │ │
│ │ Total: 6 open incidents                                 │ │
│ └─────────────────────────────────────────────────────────┘ │
│                                                             │
│ ┌─────────────────────────────────────────────────────────┐ │
│ │ TOP ALERTS (Last 24 hours)                              │ │
│ ├─────────────────────────────────────────────────────────┤ │
│ │ Time      | Customer | Alert                | Severity │ │
│ ├─────────────────────────────────────────────────────────┤ │
│ │ 14:23     | ABC Corp | Connection Down      | 🔴 Crit  │ │
│ │ 12:56     | XYZ Corp | Bandwidth > 90%      | 🟡 High  │ │
│ │ 11:42     | DEF Corp | Packet Loss > 2%     | 🟠 Med   │ │
│ │ 10:15     | GHI Inc  | Latency > 100ms      | 🟠 Med   │ │
│ │ 09:31     | JKL Ltd  | SLA At Risk          | 🟡 High  │ │
│ └─────────────────────────────────────────────────────────┘ │
│                                                             │
│ ┌─────────────────────────────────────────────────────────┐ │
│ │ CUSTOMER NETWORK STATUS                                 │ │
│ ├─────────────────────────────────────────────────────────┤ │
│ │ Customer    Service   Status  BW   Latency Availability │ │
│ ├─────────────────────────────────────────────────────────┤ │
│ │ ABC Corp    Fiber     🔴 Down  -    -      98.94% ✗    │ │
│ │ XYZ Corp    VPN       🟡 Warn  89%  52ms   99.51% ⚠    │ │
│ │ DEF Corp    Wireless  🟢 Online 45% 18ms   99.72% ✓    │ │
│ │ GHI Inc     Fiber     🟢 Online 67% 22ms   99.45% ✓    │ │
│ │ JKL Ltd     VoIP      🟢 Online 23% 15ms   99.98% ✓    │ │
│ └─────────────────────────────────────────────────────────┘ │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

---

## 11. User Flows

### 11.1 NOC Operator - Daily Workflow

```
START OF SHIFT
    ↓
Login to System
    ↓
Open Dashboard
    ├─ Check Quick Stats
    │   ├─ How many customers online?
    │   ├─ Any critical incidents?
    │   └─ Overall network health?
    ↓
├─ Normal Situation → Continue Monitoring
│   ├─ Watch real-time updates
    ├─ Review alert history
    └─ Prepare daily report
    ↓
└─ Abnormality Detected → Immediate Response
    ├─ Identify affected customer
    ├─ Open Customer Detail
    │   ├─ Check service status
    │   ├─ View network metrics
    │   │   ├─ Bandwidth
    │   │   ├─ Latency
    │   │   ├─ Packet loss
    │   │   └─ Availability
    │   ├─ Check topology
    │   └─ Review incident history
    ├─ Investigate Root Cause
    │   ├─ Check physical device status
    │   ├─ Review link status
    │   ├─ Check metrics trend
    │   └─ Correlate multiple signals
    ├─ Create/Update Incident
    │   ├─ Set severity level
    │   ├─ Add investigation notes
    │   └─ Assign to technician
    ├─ Send Notification
    │   ├─ Notify escalation group
    │   └─ Provide initial status to customer
    ├─ Monitor Recovery
    │   ├─ Watch metric changes
    │   ├─ Confirm service back online
    │   └─ Validate SLA impact
    ├─ Resolve Incident
    │   ├─ Document root cause
    │   ├─ Record resolution time
    │   └─ Close incident
    └─ Update SLA Status
        └─ Confirm SLA compliance

END OF SHIFT
    ├─ Generate daily report
    ├─ Review unresolved incidents
    ├─ Handover to next shift
    └─ Logout
```

### 11.2 Customer - Self-Service Flow

```
Customer Portal Login
    ↓
View Service Status
    ├─ Connection status
    ├─ Availability
    └─ SLA compliance
    ↓
├─ All Good → Download report or schedule review
│   └─ Export performance report (PDF)
│
└─ Issue Detected → Report to NetFly Support
    ├─ Contact support via dashboard
    ├─ Provide service details
    └─ Create support ticket
```

### 11.3 Manager - Decision-Making Flow

```
Management Dashboard Login
    ↓
Review KPIs
    ├─ SLA compliance rate
    ├─ Average MTTR
    ├─ Network health
    └─ Customer incident counts
    ↓
├─ Spot Trends → Schedule Review Meeting
│   ├─ Identify problematic customers
│   ├─ Analyze root causes
│   └─ Plan improvement actions
│
└─ Generate Report
    ├─ Monthly performance report
    ├─ SLA compliance summary
    ├─ Incident analysis
    └─ Export for stakeholder presentation
```

---

## 12. Data Models

### 12.1 Entity Relationship Diagram

```
┌──────────────────┐
│      User        │
├──────────────────┤
│ user_id (PK)    │
│ email           │
│ password_hash   │
│ name            │
│ phone           │
│ role_id (FK)    │
│ created_at      │
│ last_login      │
│ is_active       │
└──────────────────┘
        │ has
        ↓
┌──────────────────┐
│      Role        │
├──────────────────┤
│ role_id (PK)    │
│ role_name       │
│ description     │
│ permissions[]   │
└──────────────────┘


┌──────────────────┐
│    Customer      │
├──────────────────┤
│ customer_id (PK)│
│ company_name    │
│ address         │
│ city            │
│ phone           │
│ email           │
│ pic_name        │
│ pic_phone       │
│ status          │
│ contract_date   │
│ created_at      │
└──────────────────┘
        │ has
        ↓
┌──────────────────────┐
│      Service         │
├──────────────────────┤
│ service_id (PK)     │
│ customer_id (FK)    │
│ service_name        │
│ service_type        │ ← (Fiber, VPN, Wireless, VoIP)
│ status              │
│ bandwidth_capacity  │
│ sla_target          │
│ technician_id (FK)  │
│ created_at          │
│ device_id (FK)      │
└──────────────────────┘
        │ uses
        ↓
┌──────────────────────┐
│    NetworkDevice     │
├──────────────────────┤
│ device_id (PK)      │
│ device_name         │
│ device_type         │
│ ip_address          │
│ snmp_community      │
│ location            │
│ status              │
│ created_at          │
└──────────────────────┘
        │ has
        ↓
┌──────────────────────┐
│    DeviceMetric      │
├──────────────────────┤
│ metric_id (PK)      │
│ device_id (FK)      │
│ metric_type         │ ← (bandwidth, latency, packet_loss, uptime)
│ value               │
│ timestamp           │
│ unit                │
└──────────────────────┘


┌──────────────────────┐
│    ServiceMetric     │
├──────────────────────┤
│ metric_id (PK)      │
│ service_id (FK)     │
│ metric_type         │
│ value               │
│ timestamp           │
│ unit                │
└──────────────────────┘


┌──────────────────────┐
│     Incident         │
├──────────────────────┤
│ incident_id (PK)    │
│ customer_id (FK)    │
│ service_id (FK)     │
│ severity            │
│ status              │
│ title               │
│ description         │
│ detected_at         │
│ resolved_at         │
│ root_cause          │
│ assignee_id (FK)    │
│ created_at          │
│ updated_at          │
└──────────────────────┘
        │ generates
        ↓
┌──────────────────────┐
│      Alert           │
├──────────────────────┤
│ alert_id (PK)       │
│ incident_id (FK)    │
│ alert_type          │
│ threshold           │
│ current_value       │
│ severity            │
│ triggered_at        │
│ resolved_at         │
│ acknowledged_at     │
└──────────────────────┘


┌──────────────────────┐
│   SLAConfiguration   │
├──────────────────────┤
│ sla_config_id (PK)  │
│ service_id (FK)     │
│ availability_target │
│ uptime_target       │
│ max_downtime_month  │
│ response_time_sla   │
│ mttr_sla            │
│ created_at          │
└──────────────────────┘
        │ tracks
        ↓
┌──────────────────────┐
│   SLACompliance      │
├──────────────────────┤
│ sla_id (PK)         │
│ service_id (FK)     │
│ period_start        │
│ period_end          │
│ target_availability │
│ actual_availability │
│ total_downtime      │
│ incidents           │
│ status              │ ← (Met, At Risk, Breached)
│ calculated_at       │
└──────────────────────┘


┌──────────────────────┐
│    Notification      │
├──────────────────────┤
│ notification_id (PK)│
│ user_id (FK)        │
│ incident_id (FK)    │
│ alert_id (FK)       │
│ channel             │ ← (email, sms, dashboard, push)
│ message             │
│ sent_at             │
│ read_at             │
│ status              │
└──────────────────────┘
```

### 12.2 Key Data Entities

#### Customer
```javascript
{
  customer_id: "CUS-001",
  company_name: "PT ABC Indonesia",
  address: "Jl. Sudirman No. 1, Jakarta",
  city: "Jakarta",
  phone: "+62 21 1234567",
  email: "it@abc.co.id",
  pic_name: "Bambang Wijaya",
  pic_phone: "+62 812 345678",
  status: "active",
  contract_date: "2023-06-15",
  contract_end_date: "2024-06-15",
  created_at: "2023-06-15T10:00:00Z"
}
```

#### Service
```javascript
{
  service_id: "SRV-ABC-001",
  customer_id: "CUS-001",
  service_name: "Fiber Internet Jakarta",
  service_type: "Fiber", // Fiber | VPN | Wireless | VoIP
  status: "active", // active | suspended | terminated
  bandwidth_capacity: 100, // Mbps
  bandwidth_unit: "Mbps",
  sla_target: 99.5, // percentage
  technician_id: "TECH-001",
  device_id: "DEV-001", // CPE device
  created_at: "2023-06-15T10:00:00Z"
}
```

#### ServiceMetric (Time-Series)
```javascript
{
  metric_id: "MET-12345",
  service_id: "SRV-ABC-001",
  metric_type: "bandwidth_usage", // bandwidth_usage | latency | packet_loss | uptime | availability
  value: 72.5,
  unit: "%", // % | ms | Mbps
  timestamp: "2026-09-08T14:30:00Z",
  raw_value: 72500000, // in bytes
  min_value: 45,
  max_value: 92,
  avg_value: 68,
  period: "realtime" // realtime | hourly | daily
}
```

#### Incident
```javascript
{
  incident_id: "INC-2026-0091",
  customer_id: "CUS-001",
  service_id: "SRV-ABC-001",
  severity: "critical", // critical | high | medium | low
  status: "open", // open | investigating | waiting | resolved | closed
  title: "Internet Fiber Jakarta - Connection Down",
  description: "Service went offline. Likely router failure or ISP link issue.",
  detected_at: "2026-09-08T14:23:00Z",
  resolved_at: "2026-09-08T14:40:00Z", // null if not resolved
  root_cause: "CPE Router power supply failure",
  root_cause_category: "hardware", // hardware | software | configuration | network | unknown
  assignee_id: "TECH-001",
  impact_duration_minutes: 17,
  sla_impact: true, // whether this incident breaches SLA
  created_at: "2026-09-08T14:23:15Z",
  updated_at: "2026-09-08T14:40:30Z"
}
```

#### Alert
```javascript
{
  alert_id: "ALR-2026-0152",
  incident_id: "INC-2026-0091",
  service_id: "SRV-ABC-001",
  alert_type: "connection_down", // connection_down | bandwidth_high | latency_high | packet_loss | sla_at_risk | sla_breached
  threshold: 1, // threshold value (1 for boolean, percentage/ms for others)
  current_value: 0, // actual value when alert triggered
  severity: "critical", // critical | high | medium | low | info
  triggered_at: "2026-09-08T14:23:00Z",
  resolved_at: "2026-09-08T14:40:00Z", // null if not resolved
  acknowledged_at: "2026-09-08T14:24:00Z", // null if not acknowledged
  acknowledged_by: "USR-001",
  suppressed: false,
  notification_sent: true
}
```

#### SLACompliance
```javascript
{
  sla_id: "SLA-2026-SEP-ABC-001",
  service_id: "SRV-ABC-001",
  period_start: "2026-09-01T00:00:00Z",
  period_end: "2026-09-30T23:59:59Z",
  period_type: "monthly", // monthly | quarterly | yearly
  target_availability: 99.5,
  actual_availability: 99.72,
  availability_variance: 0.22, // positive = exceeding target
  total_uptime_minutes: 43245, // total time online
  total_downtime_minutes: 135, // total time offline
  incidents_count: 3,
  critical_incidents: 1,
  status: "sla_met", // sla_met | at_risk | breached
  breach_reason: null, // why SLA breached (if applicable)
  calculated_at: "2026-09-08T15:00:00Z",
  notes: "Service was highly stable this month"
}
```

---

## 13. Integrations

### 13.1 Data Source Integrations

#### SNMP Integration
- **Purpose:** Collect metrics from network devices (routers, switches, CPE)
- **Frequency:** Every 30 seconds
- **Metrics Collected:**
  - Interface status (up/down)
  - Bandwidth utilization
  - CPU usage
  - Memory usage
  - Interface errors/discards

#### Ping/ICMP Integration
- **Purpose:** Monitor connectivity and latency
- **Frequency:** Every 30 seconds
- **Metrics:**
  - Round Trip Time (RTT) / Latency
  - Packet loss percentage
  - Reachability status

#### NetFlow/sFlow Integration
- **Purpose:** Detailed traffic analysis and flow monitoring
- **Frequency:** Real-time flow data
- **Metrics:**
  - Per-protocol bandwidth usage
  - Top talkers/listeners
  - Traffic anomalies

#### Syslog Integration
- **Purpose:** Collect device events and errors
- **Frequency:** Real-time
- **Events:**
  - Interface up/down
  - Configuration changes
  - Device errors/warnings
  - BGP route changes

### 13.2 External Notification Integrations

- **Email Service**: SendGrid / AWS SES
- **SMS Service**: Twilio (optional for Phase 2)
- **Slack Integration** (future): Incident notifications to Slack channels
- **PagerDuty Integration** (future): Escalation management

### 13.3 Export & Integration APIs

- **REST API** for external systems to:
  - Query customer status
  - Retrieve metrics
  - Create/update incidents
  - Pull reports
- **Webhook** for event notifications to external systems
- **CSV Export** for reports and data analysis

---

## 14. Technology Stack

### 14.1 Frontend Stack

| Layer | Technology | Version | Purpose |
|-------|-----------|---------|---------|
| **Framework** | Vue.js | 3.x LTS | Progressive JavaScript framework |
| **State Management** | Pinia | Latest | Lightweight Vue state management |
| **Real-time Updates** | Socket.IO Client | 4.x | Real-time dashboard updates via WebSockets |
| **Styling** | Tailwind CSS | 3.x | Utility-first CSS framework for responsive UI |
| **Charts/Graphs** | Chart.js / Apex Charts | Latest | Data visualization & performance graphs |
| **UI Component Library** | Headless UI / Shadcn | Latest | Unstyled, accessible components with Tailwind |
| **HTTP Client** | Axios | Latest | REST API requests |
| **Form Handling** | VeeValidate | Latest | Form validation |
| **Date/Time** | Day.js | Latest | Date manipulation |
| **Build Tool** | Vite | Latest | Lightning-fast module bundler |
| **Testing** | Vitest + Vue Test Utils | Latest | Unit & component tests |
| **Package Manager** | npm / pnpm | Latest | Dependency management |

**Browser Support:** Chrome 90+, Firefox 88+, Safari 14+, Edge 90+

**Setup:**
```bash
npm create vite@latest -- --template vue
npm install -D tailwindcss postcss autoprefixer
npm install pinia axios socket.io-client chart.js day.js
```

### 14.2 Backend Stack

| Layer | Technology | Version | Purpose |
|-------|-----------|---------|---------|
| **Framework** | Laravel | 10.x / 11.x LTS | PHP web framework |
| **Language** | PHP | 8.2+ | Backend language |
| **API Layer** | Laravel RESTful API | Latest | REST API endpoints |
| **Authentication** | Laravel Sanctum / Passport | Latest | Token-based authentication |
| **Database ORM** | Eloquent | Latest | Object-relational mapping |
| **Validation** | Laravel Validation | Latest | Input validation & sanitization |
| **Authorization** | Laravel Policy / Gate | Latest | Role-based access control (RBAC) |
| **Real-time** | Laravel Reverb | Latest | WebSocket server for real-time updates |
| **Task Scheduling** | Laravel Scheduler | Latest | Cron job management |
| **Logging** | Monolog (built-in) | Latest | Application logging |
| **Testing** | PHPUnit + Pest | Latest | Unit & feature tests |
| **Package Manager** | Composer | Latest | PHP dependency management |
| **Code Quality** | Laravel Pint / PHPStan | Latest | Code analysis & formatting |

**Setup:**
```bash
composer create-project laravel/laravel netfly-nms
composer require laravel/reverb
php artisan reverb:install
```

**Key Packages:**
- `laravel/sanctum` — API authentication
- `spatie/laravel-query-builder` — Dynamic API queries
- `spatie/laravel-permission` — Advanced RBAC
- `barryvdh/laravel-dompdf` — PDF generation
- `maatwebsite/excel` — Excel export

### 14.3 Network Monitoring & Workers

| Component | Technology | Version | Purpose |
|-----------|-----------|---------|---------|
| **Ping Monitor** | PHP / Python script | Latest | ICMP ping for connectivity checks |
| **Task Scheduler** | Laravel Scheduler | Latest | Recurring ping & collection jobs |
| **Daemon Process** | Supervisor | Latest | Keep monitoring scripts running 24/7 |
| **Scheduled Tasks** | `php artisan schedule:run` | Latest | Execute monitoring every 30 seconds |
| **Network Utilities** | net-tools / ping / nmap | Latest | Underlying network commands |

**Alternative (Optional):**
- **Node.js Daemon** — If preferring Node.js for specific collectors
- **Python Scripts** — For advanced SNMP/NetFlow processing

**Monitoring Loop (Laravel):**
```php
// app/Console/Kernel.php
$schedule->command('monitor:ping-devices')->everyThirtySeconds();
$schedule->command('monitor:collect-snmp')->everyThirtySeconds();
$schedule->command('monitor:calculate-sla')->everyMinute();
$schedule->command('monitor:check-alerts')->everyMinute();
```

### 14.4 Database Stack

| Component | Technology | Version | Purpose |
|-----------|-----------|---------|---------|
| **Relational DB** | MySQL / MariaDB | 8.0+ | Core data storage (customers, services, incidents) |
| **Time-Series Storage** | MySQL Partitioning | Latest | Historical metrics in MySQL tables |
| **Query Caching** | Redis | 6+ | Session cache & hot data |
| **Background Jobs** | Laravel Queue (Redis/Database) | Latest | Async job processing |

**Database Schema Highlights:**
```sql
-- Core tables
customers, services, network_devices
incidents, alerts, sla_configurations

-- Metrics tables (partitioned by time)
device_metrics (device_id, metric_type, value, timestamp)
service_metrics (service_id, metric_type, value, timestamp)

-- Audit & logs
incident_logs, alert_history, user_audit_logs
```

**Why MySQL for Time-Series:**
- Integrated with Laravel ecosystem
- Table partitioning for large datasets
- Native support for timestamps
- Cost-effective for small-to-medium scale

### 14.5 Real-time Communication Layer

| Component | Technology | Purpose |
|-----------|-----------|---------|
| **WebSocket Server** | Laravel Reverb | Real-time metric push to clients |
| **Broadcasting** | Laravel Broadcasting | Publish/subscribe for live updates |
| **Client Library** | Socket.IO JavaScript | Frontend WebSocket connection |
| **Channel Types** | Public/Private/Presence | Event isolation & security |

**Real-time Flow:**
```
Network Device (Ping successful)
    ↓
Laravel Job (detects status change)
    ↓
Laravel Reverb (broadcasts status.updated event)
    ↓
Socket.IO Client (receives in browser)
    ↓
Vue.js State (updates Pinia store)
    ↓
Dashboard UI (re-renders with new status)
```

**Setup:**
```bash
composer require laravel/reverb
php artisan reverb:install

# .env
REVERB_APP_ID=netfly-nms
REVERB_APP_KEY=your-key
REVERB_SERVER_HOST=0.0.0.0
REVERB_SERVER_PORT=8080
```

### 14.6 Infrastructure & Deployment

| Component | Technology | Purpose |
|-----------|-----------|---------|
| **Web Server** | Nginx | Reverse proxy & load balancing |
| **PHP Execution** | PHP-FPM | FastCGI Process Manager |
| **Containerization** | Docker | Container images for services |
| **Composition** | Docker Compose | Multi-container orchestration |
| **Task Queue** | Laravel Queue + Redis | Async job processing |
| **Monitoring** | Prometheus / New Relic | Application performance monitoring |
| **Log Aggregation** | ELK Stack / Laravel Logs | Centralized logging |
| **CI/CD** | GitHub Actions / GitLab CI | Automated testing & deployment |
| **Hosting** | AWS EC2 / DigitalOcean / VPS | Cloud infrastructure |

### 14.7 Development Environment (Docker)

```yaml
# docker-compose.yml
version: '3.8'

services:
  # Nginx web server
  nginx:
    image: nginx:latest
    ports:
      - "80:80"
    volumes:
      - ./:/app
      - ./docker/nginx.conf:/etc/nginx/nginx.conf

  # PHP-FPM application
  app:
    build:
      context: .
      dockerfile: docker/Dockerfile
    volumes:
      - ./:/app
    environment:
      - APP_ENV=local
      - DB_HOST=mysql

  # MySQL database
  mysql:
    image: mysql:8.0
    environment:
      MYSQL_DATABASE: netfly_nms
      MYSQL_ROOT_PASSWORD: root
    volumes:
      - mysql_data:/var/lib/mysql
    ports:
      - "3306:3306"

  # Redis cache & queue
  redis:
    image: redis:7-alpine
    ports:
      - "6379:6379"

  # Laravel Reverb WebSocket
  reverb:
    build:
      context: .
      dockerfile: docker/Dockerfile
    command: php artisan reverb:start
    environment:
      - REVERB_SERVER_HOST=0.0.0.0
      - REVERB_SERVER_PORT=8080
    ports:
      - "8080:8080"

  # Laravel Queue Worker
  queue:
    build:
      context: .
      dockerfile: docker/Dockerfile
    command: php artisan queue:work redis --sleep=3 --tries=3
    environment:
      - DB_HOST=mysql

volumes:
  mysql_data:
```

### 14.8 System Architecture (Updated)

```
┌────────────────────────────────────────────────────┐
│ CLIENT LAYER                                       │
│ ┌──────────────────────────────────────────────┐  │
│ │ Vue.js 3 + Tailwind CSS UI                   │  │
│ │ • Dashboard (real-time KPI)                  │  │
│ │ • Customer Management                        │  │
│ │ • Network Monitoring                         │  │
│ │ • Incident Management                        │  │
│ │ • Reporting & Analytics                      │  │
│ │ • WebSocket (Socket.IO) for live updates     │  │
│ └──────────────────────────────────────────────┘  │
└────────────┬─────────────────────────────────────┘
             │ REST API + WebSocket
┌────────────▼─────────────────────────────────────┐
│ APPLICATION LAYER (Nginx → PHP-FPM)              │
│ ┌──────────────────────────────────────────────┐ │
│ │ Laravel 11.x API Server                      │ │
│ │ • User Management & Auth (Sanctum)           │ │
│ │ • Customer & Service Management              │ │
│ │ • Dashboard Data Endpoints                   │ │
│ │ • Monitoring Service                         │ │
│ │ • Alert & Notification Service               │ │
│ │ • SLA Calculation Service                    │ │
│ │ • Incident Management Service                │ │
│ │ • Report Generation Service                  │ │
│ │ • Configuration Service                      │ │
│ │ • Laravel Reverb (WebSocket Server)          │ │
│ └──────────────────────────────────────────────┘ │
└────────────┬─────────────────────────────────────┘
             │
┌────────────┼──────────────┬──────────────────┐
│            │              │                  │
▼            ▼              ▼                  ▼
MySQL      Redis      Laravel Queue      Reverb
(Data)   (Cache)     (Async Jobs)    (WebSockets)
└────────────┴──────────────┴──────────────────┘

┌────────────────────────────────────────────────────┐
│ MONITORING WORKERS LAYER                           │
│ ┌──────────────────────────────────────────────┐  │
│ │ Laravel Scheduler + Supervisor               │  │
│ │ • Ping Monitor (every 30 sec)                │  │
│ │ • SNMP Collector (every 30 sec)              │  │
│ │ • SLA Calculator (every minute)              │  │
│ │ • Alert Checker (every minute)               │  │
│ │ • Log Cleanup (daily)                        │  │
│ │ • Report Generator (scheduled)               │  │
│ │                                              │  │
│ │ Connectivity: Broadcasts events via Reverb   │  │
│ └──────────────────────────────────────────────┘  │
└────────────┬─────────────────────────────────────┘
             │
┌────────────▼─────────────────────────────────────┐
│ NETWORK DATA SOURCES                             │
│ • Customer CPE / Routers (SNMP)                  │
│ • Network Devices (Ping/ICMP)                    │
│ • Syslog Events (future)                         │
│ • NetFlow Data (future)                          │
└──────────────────────────────────────────────────┘
```

### 14.9 Development Dependencies

```json
{
  "require": {
    "php": "^8.2",
    "laravel/framework": "^11.0",
    "laravel/reverb": "^0.1",
    "laravel/sanctum": "^3.0",
    "spatie/laravel-permission": "^6.0",
    "spatie/laravel-query-builder": "^5.0"
  },
  "require-dev": {
    "phpunit/phpunit": "^10.0",
    "pestphp/pest": "^2.0",
    "laravel/pint": "^1.0",
    "phpstan/phpstan": "^1.0"
  }
}
```

**Frontend:**
```json
{
  "dependencies": {
    "vue": "^3.3.0",
    "pinia": "^2.1.0",
    "axios": "^1.6.0",
    "socket.io-client": "^4.7.0",
    "chart.js": "^4.4.0",
    "day.js": "^1.11.0",
    "vee-validate": "^4.12.0"
  },
  "devDependencies": {
    "vite": "^5.0.0",
    "tailwindcss": "^3.3.0",
    "postcss": "^8.4.0",
    "autoprefixer": "^10.4.0",
    "@vitejs/plugin-vue": "^5.0.0",
    "vitest": "^1.0.0",
    "@vue/test-utils": "^2.4.0"
  }
}
```

### 14.10 Deployment Checklist

**Pre-Production:**
- [ ] PHP 8.2+ installed on server
- [ ] Composer dependencies installed
- [ ] MySQL database created & migrated
- [ ] Redis running for cache & queue
- [ ] Laravel Reverb configured & running
- [ ] Nginx configured for Laravel
- [ ] SSL certificate installed
- [ ] Environment variables (.env) configured
- [ ] Storage permissions set correctly

**Deployment Command:**
```bash
# Pull latest code
git pull origin main

# Install dependencies
composer install --no-dev --optimize-autoloader

# Run migrations
php artisan migrate --force

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start queue & scheduler
php artisan queue:restart
php artisan schedule:run

# Start Reverb WebSocket
php artisan reverb:start --host=0.0.0.0 --port=8080
```

---

## 15. Non-Functional Requirements

### 15.1 Performance Requirements

| Requirement | Target | Measurement |
|-------------|--------|-------------|
| **Dashboard Load Time** | < 2 seconds | Page load time from browser |
| **API Response Time** | < 500ms (p95) | REST API response latency |
| **Real-time Update Delay** | < 1 minute | Time from metric collection to dashboard display |
| **Concurrent Users** | 100+ simultaneous | Load testing (k6 / JMeter) |
| **Database Query Time** | < 100ms (p95) | Database query latency |
| **Report Generation** | < 30 seconds | Time to generate full monthly report |

### 15.2 Availability & Reliability

| Requirement | Target | Details |
|-------------|--------|---------|
| **System Uptime** | 99.9% | SLA 99.9% availability |
| **MTBF** | > 720 hours | Mean Time Between Failures |
| **MTTR** | < 30 minutes | Mean Time To Repair |
| **Data Backup** | Daily | Automated backups every 24h |
| **Disaster Recovery** | < 1 hour RTO | Recovery Time Objective |
| **Data Retention** | 24 months | Minimum retention period |

### 15.3 Scalability

| Requirement | Target | Notes |
|-------------|--------|-------|
| **Customers Supported** | 500+ | Per NetFly deployment |
| **Devices Monitored** | 2000+ | Routers, switches, CPE |
| **Metrics Per Second** | 10,000+ | Metrics collection rate |
| **Historical Data Points** | 100 million+ | 2 years of data |
| **Horizontal Scaling** | Yes | Kubernetes auto-scaling |

### 15.4 Security Requirements

| Requirement | Implementation | Priority |
|-------------|----------------|----------|
| **Authentication** | JWT + MFA | P0 |
| **Authorization** | Role-Based Access Control (RBAC) | P0 |
| **Encryption in Transit** | TLS 1.3 | P0 |
| **Encryption at Rest** | AES-256 | P0 |
| **Password Policy** | Min 12 chars, complexity rules | P0 |
| **Session Timeout** | 30 minutes inactivity | P0 |
| **Audit Logging** | All user actions logged | P0 |
| **SQL Injection Prevention** | Parameterized queries | P0 |
| **CSRF Protection** | CSRF tokens | P0 |
| **Rate Limiting** | API rate limiting (100 req/min per user) | P1 |
| **Penetration Testing** | Quarterly security audits | P1 |
| **Compliance** | ISO 27001, GDPR ready | P1 |

### 15.5 Usability Requirements

| Requirement | Details |
|-------------|---------|
| **Responsiveness** | Mobile-responsive design (tablet & desktop) |
| **Accessibility** | WCAG 2.1 AA compliance |
| **Localization** | Support Indonesian & English (future) |
| **Help & Documentation** | In-app help, user guide, video tutorials |
| **Intuitive UI** | Dashboard should be understandable within 5 minutes |

---

## 16. Acceptance Criteria

### 16.1 Dashboard

- [ ] System displays real-time KPI summary (online/offline/warning/critical count)
- [ ] KPI numbers update automatically every 30 seconds
- [ ] Network Health percentage is calculated and displayed correctly
- [ ] Active Incidents section shows current open incidents
- [ ] Critical incidents are highlighted prominently (red)
- [ ] Dashboard is responsive on desktop (1920px), tablet (768px), and mobile (375px)
- [ ] Page loads in < 2 seconds on standard broadband
- [ ] Real-time charts update smoothly without page refresh

### 16.2 Customer Management

- [ ] Admin can add new customer with all required fields
- [ ] Admin can edit customer information
- [ ] Customer list shows all corporate customers with status
- [ ] Search/filter functionality works on customer list
- [ ] Each customer has associated services
- [ ] Customer contact information is properly validated
- [ ] Customer deletion is possible (with confirmation)
- [ ] Customer status can be toggled (active/suspended/terminated)

### 16.3 Service Management

- [ ] Admin can create service under a customer
- [ ] Service type can be set (Fiber, VPN, Wireless, VoIP)
- [ ] Bandwidth capacity is configurable per service
- [ ] SLA target is configurable per service
- [ ] Each service can be assigned a device (CPE)
- [ ] Service status reflects device connection status
- [ ] Multiple services can be assigned to one customer
- [ ] Service deletion removes associated monitoring data (with confirmation)

### 16.4 Network Monitoring

- [ ] System collects SNMP data from configured devices every 30 seconds
- [ ] Device status reflects SNMP connectivity (online/offline/warning)
- [ ] Interface status is displayed (up/down)
- [ ] Bandwidth metrics are collected and stored
- [ ] Latency is measured via ping/ICMP every 30 seconds
- [ ] Packet loss is calculated from ping probes
- [ ] Metrics are persisted in time-series database
- [ ] Historical data is retrievable for trend analysis
- [ ] Device uptime/downtime is calculated automatically
- [ ] Topology view shows device relationships

### 16.5 Bandwidth Monitoring

- [ ] Current bandwidth usage is displayed in real-time
- [ ] Download and upload are tracked separately
- [ ] Bandwidth utilization percentage is calculated correctly
- [ ] Bandwidth graph shows 24-hour trend
- [ ] Peak bandwidth is identified and displayed
- [ ] Alerts trigger when bandwidth > 80% (warning)
- [ ] Alerts trigger when bandwidth > 90% (critical)
- [ ] Bandwidth capacity can be adjusted per service

### 16.6 Latency & Packet Loss

- [ ] Latency is displayed in milliseconds (ms)
- [ ] Packet loss is displayed as percentage (%)
- [ ] Latency graph shows hourly/daily trend
- [ ] Packet loss history is maintained
- [ ] Alerts trigger when latency > 50ms (warning)
- [ ] Alerts trigger when latency > 100ms (critical)
- [ ] Alerts trigger when packet loss > 0.5% (warning)
- [ ] Alerts trigger when packet loss > 2% (critical)
- [ ] Average, min, max values are calculated

### 16.7 Availability Monitoring

- [ ] Uptime percentage is calculated per service
- [ ] Downtime duration is tracked per incident
- [ ] Historical downtime is stored (past 24 months)
- [ ] Availability trend is displayed on graph
- [ ] System distinguishes between scheduled and unscheduled downtime
- [ ] Downtime reason can be documented

### 16.8 SLA Monitoring

- [ ] SLA target is configurable per customer/service
- [ ] Actual availability is calculated automatically
- [ ] System compares actual vs target continuously
- [ ] Status is displayed: SLA Met / At Risk / Breached
- [ ] "At Risk" warning appears when margin < 0.1%
- [ ] SLA breach triggers critical alert
- [ ] SLA history is maintained (monthly records)
- [ ] SLA compliance report can be generated on-demand
- [ ] Customer can view own SLA status

### 16.9 Incident Management

- [ ] Incidents are automatically created when alerts trigger
- [ ] Manual incident creation is allowed (NOC can create incident)
- [ ] Incident has unique ID (auto-generated)
- [ ] Incident status has workflow: Open → Investigating → Resolved → Closed
- [ ] Incident can be assigned to technician
- [ ] Incident severity can be set (critical/high/medium/low)
- [ ] Incident timeline shows all events (detection, acknowledgement, resolution)
- [ ] Root cause can be documented
- [ ] Incident duration is calculated automatically
- [ ] Incidents can be searched/filtered
- [ ] Incident history is maintained (audit trail)

### 16.10 Alert Management

- [ ] Alerts are triggered based on configurable thresholds
- [ ] Alert types include: connection_down, bandwidth_high, latency_high, packet_loss, sla_at_risk, sla_breached
- [ ] Alert threshold values can be edited per alert type
- [ ] Alerts can be acknowledged by NOC
- [ ] Alerts can be suppressed (temporarily disabled)
- [ ] Alert history is maintained
- [ ] Escalation happens after 15 minutes unresolved
- [ ] Critical alerts cannot be suppressed indefinitely

### 16.11 Notifications

- [ ] Email notifications sent for critical incidents
- [ ] Dashboard notifications appear in real-time
- [ ] SMS notifications sent for critical incidents (phase 2)
- [ ] Notification preferences can be configured per user
- [ ] Do Not Disturb (DND) schedule can be set
- [ ] Notification templates can be customized
- [ ] Notification delivery status is tracked
- [ ] Unread notification badge appears on dashboard

### 16.12 Reporting

- [ ] Performance report shows bandwidth, latency, packet loss, uptime metrics
- [ ] Report can be generated for custom date range
- [ ] Report includes graphs and tables
- [ ] Report can be exported to PDF
- [ ] Report can be exported to Excel
- [ ] SLA Compliance report shows target vs actual
- [ ] Incident report lists all incidents in period
- [ ] Report includes root cause analysis
- [ ] Scheduled reports can be configured (daily/weekly/monthly)
- [ ] Reports can be emailed automatically
- [ ] Customer can access own reports

### 16.13 User Management

- [ ] Admin can create user accounts
- [ ] Admin can assign roles to users
- [ ] Role-based access control (RBAC) is enforced
- [ ] Permissions are restricted based on role
- [ ] User login requires email and password
- [ ] Password reset functionality works
- [ ] MFA (Multi-Factor Authentication) is available for sensitive accounts
- [ ] Session timeout after 30 minutes inactivity
- [ ] User activity is logged (audit trail)
- [ ] User can only see data relevant to their role

### 16.14 Configuration & Settings

- [ ] Alert thresholds can be configured globally
- [ ] Alert thresholds can be overridden per customer
- [ ] SLA templates can be created
- [ ] Notification rules can be configured
- [ ] Integration settings (SNMP, Syslog) can be configured
- [ ] System preferences (language, timezone, date format) can be set
- [ ] Backup schedule can be configured
- [ ] Data retention policy can be configured
- [ ] Settings changes are logged

---

## 17. Implementation Roadmap

### 17.0 Laravel-Specific Implementation Pattern

**Key Pattern: Scheduler-driven Monitoring Commands**

```php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    // Ping monitor every 30 seconds
    $schedule->command('monitor:ping-devices')
        ->everyThirtySeconds()
        ->withoutOverlapping()
        ->onFailure(function () {
            Log::error('Ping monitor failed');
        });

    // SNMP collector every 30 seconds
    $schedule->command('monitor:collect-snmp')
        ->everyThirtySeconds()
        ->withoutOverlapping();

    // SLA calculation every minute
    $schedule->command('monitor:calculate-sla')
        ->everyMinute()
        ->withoutOverlapping();

    // Alert checking every minute
    $schedule->command('monitor:check-alerts')
        ->everyMinute()
        ->withoutOverlapping();

    // Cleanup logs daily
    $schedule->command('queue:restart')
        ->daily();
}

// Run with: php artisan schedule:work
```

**Example Monitoring Command:**

```php
// app/Console/Commands/PingDevicesCommand.php
class PingDevicesCommand extends Command
{
    protected $signature = 'monitor:ping-devices';
    protected $description = 'Ping all active devices and update status';

    public function handle()
    {
        $devices = Device::where('status', 'active')->get();
        
        foreach ($devices as $device) {
            $pingable = Ping::check($device->ip_address);
            $latency = Ping::latency($device->ip_address);
            
            // Store metric in MySQL
            DeviceMetric::create([
                'device_id' => $device->id,
                'metric_type' => 'connectivity',
                'value' => $pingable ? 1 : 0,
                'latency' => $latency,
                'timestamp' => now(),
            ]);
            
            // Broadcast via Reverb if status changed
            if ($device->status != ($pingable ? 'online' : 'offline')) {
                $device->update(['status' => $pingable ? 'online' : 'offline']);
                
                broadcast(new DeviceStatusChanged($device));
            }
        }
    }
}
```

**Running Scheduler:**

```bash
# In production (via supervisor)
php artisan schedule:work

# Or use cron to call every minute:
* * * * * cd /app && php artisan schedule:run >> /dev/null 2>&1
```

**Real-time Broadcasting with Laravel Reverb:**

```php
// app/Events/DeviceStatusChanged.php
class DeviceStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Device $device;

    public function __construct(Device $device)
    {
        $this->device = $device;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('device.status'),
            new PrivateChannel('customer.' . $this->device->customer_id),
        ];
    }

    public function broadcastAs()
    {
        return 'device.status.changed';
    }
}

// Dispatch from monitoring command:
broadcast(new DeviceStatusChanged($device));
```

**Vue.js Client - Real-time Updates:**

```javascript
// src/services/websocket.js
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
// Or use Laravel Reverb:
window.Pusher = Pusher;

const echo = new Echo({
    broadcaster: 'reverb',
    key: 'your-app-key',
    wsHost: window.location.hostname,
    wsPort: 8080,
    wssPort: 443,
    forceTLS: import.meta.env.PROD,
    enabledTransports: ['ws', 'wss'],
});

// Subscribe to real-time updates
export function watchDeviceStatus(customerId, callback) {
    echo.private(`customer.${customerId}`)
        .listen('DeviceStatusChanged', (data) => {
            callback(data.device);
        });
}

// Use in Vue component:
// src/components/DashboardKPI.vue
<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { watchDeviceStatus } from '@/services/websocket';
import { fetchKPI } from '@/api/dashboard';

const kpi = ref(null);

onMounted(async () => {
    kpi.value = await fetchKPI();
    
    // Listen for real-time updates
    watchDeviceStatus(currentUser.customerId, (device) => {
        if (device.status === 'online') {
            kpi.value.online++;
            kpi.value.offline--;
        } else {
            kpi.value.online--;
            kpi.value.offline++;
        }
    });
});
</script>

<template>
    <div class="grid grid-cols-4 gap-4">
        <div class="card">
            <h3>Online</h3>
            <p class="text-3xl font-bold">{{ kpi.online }}</p>
        </div>
        <div class="card">
            <h3>Offline</h3>
            <p class="text-3xl font-bold text-red-600">{{ kpi.offline }}</p>
        </div>
        <!-- More KPI cards -->
    </div>
</template>
```

**Laravel Reverb Configuration (.env):**

```env
REVERB_APP_ID=netfly-nms
REVERB_APP_KEY=your-secret-key
REVERB_SERVER_HOST=0.0.0.0
REVERB_SERVER_PORT=8080
REVERB_SCHEME=http

# For production with SSL:
REVERB_SCHEME=https
REVERB_HOST=monitoring.netfly.id
REVERB_PORT=443
```

---

### 17.1 Phase 1: Foundation (Months 1-2)

**Goal:** Build core monitoring infrastructure and basic dashboard

**Deliverables:**

1. **Project Setup**
   - ✅ Development environment setup (Docker, Git)
   - ✅ Database schema creation (PostgreSQL)
   - ✅ API skeleton (Node.js + Express)
   - ✅ Frontend scaffolding (React/Vue)

2. **Core Features**
   - ✅ User authentication & role management
   - ✅ Customer CRUD operations
   - ✅ Service CRUD operations
   - ✅ Dashboard - KPI summary
   - ✅ SNMP data collector
   - ✅ Ping/ICMP latency monitor

3. **Basic Monitoring**
   - ✅ Connection status (online/offline)
   - ✅ Bandwidth tracking (basic)
   - ✅ Latency measurement
   - ✅ Real-time metric storage (InfluxDB)

4. **Deliverables**
   - Deployment environment (staging)
   - API documentation (Swagger)
   - Database backup strategy
   - User documentation (basic)

**Sprint Schedule:** 2-week sprints × 4 sprints

---

### 17.2 Phase 2: Operational Features (Months 3-4)

**Goal:** Enable NOC operations and incident management

**Deliverables:**

1. **Monitoring Enhancement**
   - ✅ Packet loss tracking
   - ✅ Availability calculation
   - ✅ Network topology visualization
   - ✅ Performance graphs & trends

2. **Incident Management**
   - ✅ Incident creation (auto & manual)
   - ✅ Incident status workflow
   - ✅ Assignment & escalation
   - ✅ Timeline & history

3. **Alert System**
   - ✅ Threshold configuration
   - ✅ Alert generation & delivery
   - ✅ Alert escalation (15 min rule)
   - ✅ Alert suppression

4. **SLA Management**
   - ✅ SLA target configuration
   - ✅ Real-time SLA calculation
   - ✅ SLA compliance status (Met/At Risk/Breached)
   - ✅ Basic SLA report

5. **Notifications**
   - ✅ Email notification service
   - ✅ Dashboard notifications
   - ✅ Notification preferences
   - ✅ DND schedule

6. **Deliverables**
   - Production deployment
   - Incident management workflow documentation
   - NOC user training
   - Performance optimization

**Sprint Schedule:** 2-week sprints × 4 sprints

---

### 17.3 Phase 3: Advanced Features & Customer Portal (Months 5-6)

**Goal:** Enable advanced analytics, reporting, and customer self-service

**Deliverables:**

1. **Advanced Analytics**
   - ✅ Trend analysis & forecasting
   - ✅ Anomaly detection
   - ✅ Root cause correlation
   - ✅ Custom dashboards

2. **Comprehensive Reporting**
   - ✅ Performance report (PDF/Excel)
   - ✅ SLA compliance report
   - ✅ Incident analysis report
   - ✅ Scheduled report delivery
   - ✅ Custom report builder

3. **Customer Portal**
   - ✅ Customer login & authentication
   - ✅ View own service status
   - ✅ View own metrics & performance
   - ✅ Access SLA reports
   - ✅ Download historical reports

4. **Integration Enhancements**
   - ✅ NetFlow/sFlow integration (optional)
   - ✅ Syslog integration
   - ✅ API for external systems
   - ✅ Webhook support

5. **System Hardening**
   - ✅ Performance optimization
   - ✅ Security audit & fixes
   - ✅ Data migration tools
   - ✅ Disaster recovery procedures

6. **Deliverables**
   - Production release (stable version)
   - Comprehensive documentation
   - Training programs (NOC, Admin, Customer)
   - Go-live support

**Sprint Schedule:** 2-week sprints × 4 sprints

---

### 17.4 Detailed Sprint Example (Sprint 1)

**Sprint: Phase 1, Sprint 1 (Week 1-2)**

**Sprint Goal:** Laravel foundation, database schema, and user authentication

**User Stories:**

1. **As a** developer, **I want** Docker Compose setup with Laravel, MySQL, Redis, and Nginx configured, **so that** I can quickly spin up consistent local development environment
   - **Acceptance:** Docker Compose runs `php artisan serve` and Nginx accessible at localhost
   - **Tasks:**
     - Create docker-compose.yml with MySQL 8.0, Redis 7, PHP 8.2, Nginx
     - Setup docker/Dockerfile with PHP extensions (bcmath, ctype, json, mbstring, openssl, pdo, tokenizer, xml)
     - Create docker/nginx.conf for Laravel routing
     - Test: `docker-compose up` and access http://localhost in browser

2. **As a** developer, **I want** Laravel project initialized with essential packages, **so that** I have foundation to build features
   - **Acceptance:** `composer install` completes, `php artisan migrate` runs successfully
   - **Tasks:**
     - Create Laravel 11.x project
     - Install packages: `laravel/sanctum`, `spatie/laravel-permission`, `spatie/laravel-query-builder`
     - Setup .env configuration
     - Create database migrations structure
     - Test: `php artisan tinker` works

3. **As a** developer, **I want** API skeleton with base middleware (CORS, error handling, logging) configured, **so that** I have solid foundation for endpoints
   - **Acceptance:** API responds with proper CORS headers, errors return JSON format
   - **Tasks:**
     - Setup routes/api.php with API grouping
     - Configure CORS in cors.php
     - Create exception handler for JSON responses
     - Add request/response logging via Monolog
     - Create base API response formatter (JsonResource)

4. **As a** admin, **I want** to create user accounts with email/password via CLI or dashboard, **so that** users can access the system
   - **Acceptance:** `php artisan make:user` command works or admin panel allows user creation
   - **Tasks:**
     - Install Sanctum: `php artisan install:api`
     - Create User migration if not exists
     - Setup Hash password in User model
     - Create UserSeeder for initial admin user
     - Create API endpoint: POST /api/admin/users
     - Create dashboard form: Create User form with role selection

5. **As a** user, **I want** to login with email and password to receive API token, **so that** I can access protected endpoints
   - **Acceptance:** POST /api/login returns token, token works for subsequent requests
   - **Tasks:**
     - Create AuthController with login method
     - Implement Sanctum token generation
     - Create request validation (LoginRequest)
     - Setup auth:sanctum middleware
     - Test: Login → use token → access protected endpoint

6. **As a** admin, **I want** to assign roles (Super Admin, Network Admin, NOC, Support, Manager) to users, **so that** permissions are properly controlled
   - **Acceptance:** Users have roles, roles have permissions, middleware enforces access
   - **Tasks:**
     - Install spatie/laravel-permission
     - Create Permission & Role models
     - Seed default roles with permissions
     - Create RoleController for admin UI
     - Implement authorize middleware: Gate::authorize('admin-only')
     - Test: Different user roles have different dashboard access

7. **As a** user, **I want** a dashboard showing basic KPI (total customers, online, offline), **so that** I can see system overview
   - **Acceptance:** Dashboard loads with live KPI numbers, updates as data changes
   - **Tasks:**
     - Create Customer migration
     - Create DashboardController (API endpoint)
     - Create API endpoint: GET /api/dashboard/kpi
     - Create Vue.js DashboardView component
     - Fetch from: GET /api/dashboard/kpi
     - Display KPI cards: Total Customers, Online, Offline, Warning
     - Setup mock data or connect to database query

**Deliverables:**
- Docker Compose configuration file (docker-compose.yml)
- Laravel application initialized (composer.lock)
- Database schema (users, roles, permissions, customers table created)
- API Authentication endpoints:
  - POST /api/login (returns token)
  - POST /api/logout
  - GET /api/me (authenticated user info)
- Dashboard API endpoint:
  - GET /api/dashboard/kpi (returns KPI data)
- Frontend (Vue.js):
  - Login page (form submission → token storage in localStorage)
  - Dashboard page (KPI cards, mock data)
- Docker setup can be started with: `docker-compose up`
- Database migrations can be run with: `php artisan migrate`
- Initial admin user seeded via: `php artisan db:seed`

**Tech Deliverables:**
```
Laravel Project Structure
├── app/
│   ├── Http/Controllers/AuthController.php
│   ├── Http/Controllers/DashboardController.php
│   ├── Models/User.php
│   ├── Models/Role.php
│   └── Exceptions/Handler.php
├── database/
│   ├── migrations/
│   │   ├── users_table.php
│   │   ├── roles_table.php
│   │   └── customers_table.php
│   └── seeders/DatabaseSeeder.php
├── routes/
│   ├── api.php
│   └── web.php
└── docker-compose.yml

Vue.js Structure
├── src/
│   ├── views/
│   │   ├── LoginView.vue
│   │   └── DashboardView.vue
│   ├── stores/
│   │   └── authStore.js
│   └── App.vue
└── package.json
```

---

### 17.5 High-Level Timeline

```
Month 1-2 (Phase 1)
├─ Week 1-2: Setup & Auth
├─ Week 3-4: Basic Monitoring
├─ Week 5-6: Data Collection
└─ Week 7-8: Dashboard v1

Month 3-4 (Phase 2)
├─ Week 9-10: Incident Management
├─ Week 11-12: SLA Calculation
├─ Week 13-14: Alert System
└─ Week 15-16: Production Deployment

Month 5-6 (Phase 3)
├─ Week 17-18: Customer Portal
├─ Week 19-20: Advanced Reporting
├─ Week 21-22: Analytics & Forecasting
└─ Week 23-24: Go-Live & Support

TOTAL: 6 months to production
```

---

## 18. KPIs & Success Metrics

### 18.1 Business KPIs

| KPI | Target | Baseline | Measurement |
|-----|--------|----------|-------------|
| **SLA Compliance Rate** | > 99.5% | 98.2% (estimated) | % of services meeting SLA target |
| **System Uptime** | 99.9% | N/A | System availability monitoring |
| **Customer Satisfaction** | > 4.5/5 | N/A | Post-incident survey |
| **MTTR Reduction** | 40% (to 27 min) | 45 min (current) | Avg incident resolution time |
| **Alert Accuracy** | > 90% | N/A | True positive rate (TP / TP+FP) |
| **False Alert Rate** | < 10% | N/A | False positives per month |

### 18.2 Operational KPIs

| KPI | Target | Measurement Method |
|-----|--------|-------------------|
| **Incident Detection Time** | < 2 minutes | Time from issue start to alert |
| **Incident Acknowledgement Time** | < 5 minutes | Time from alert to NOC acknowledgement |
| **Data Collection Latency** | < 1 minute | Time from metric collection to dashboard |
| **Report Generation Time** | < 30 seconds | Time to generate standard report |
| **API Response Time (p95)** | < 500ms | API latency monitoring |

### 18.3 User Adoption KPIs

| KPI | Target | Measurement |
|-----|--------|-------------|
| **NOC Dashboard Usage** | 100% | Daily active users (NOC team) |
| **Report Generation** | 50+ reports/month | Automated + manual reports |
| **Customer Portal Adoption** | > 70% | % of corporate customers accessing portal |
| **Ticket Reduction** | 30% fewer escalations | Support ticket volume |

### 18.4 Technical KPIs

| KPI | Target | Details |
|-----|--------|---------|
| **System Availability** | 99.9% | Monthly uptime percentage |
| **Database Performance** | < 100ms (p95) | Query latency |
| **Concurrent Users** | Support 100+ | Load testing validation |
| **Data Retention** | 24 months | Historical data available |
| **Metric Storage Growth** | < 500GB/year | Time-series database size |

---

## 19. Risks & Mitigation

### 19.1 Technical Risks

| Risk | Probability | Impact | Mitigation |
|------|-------------|--------|-----------|
| **SNMP Collection Failures** | Medium | High | • Implement retry logic with exponential backoff<br>• Alert on collector failures<br>• Manual device addition fallback |
| **Database Performance** | Medium | High | • Use time-series DB (InfluxDB) for metrics<br>• Implement data partitioning<br>• Regular performance tuning |
| **Real-time Update Delays** | Low | Medium | • Use WebSocket for live updates<br>• Implement message queue for reliability<br>• Performance testing & optimization |
| **Data Accuracy/Sync Issues** | Medium | Medium | • Data validation at collection point<br>• Reconciliation jobs<br>• Audit trails |
| **Scalability Bottleneck** | Low | High | • Kubernetes for horizontal scaling<br>• Load testing early<br>• Microservices architecture ready |

### 19.2 Operational Risks

| Risk | Probability | Impact | Mitigation |
|------|-------------|--------|-----------|
| **SNMP Community String Exposure** | Medium | High | • Encrypt credentials in database<br>• Use SNMPv3 when possible<br>• Access control & audit logging |
| **False Positive Alerts** | Medium | Medium | • Tunable thresholds<br>• Alert suppression mechanism<br>• ML-based filtering (Phase 3) |
| **Alert Fatigue** | Medium | Medium | • Smart threshold tuning<br>• Grouping related alerts<br>• DND schedule for non-critical |
| **SLA Calculation Errors** | Low | High | • Comprehensive testing of logic<br>• Independent verification<br>• Audit trail of calculations |

### 19.3 Adoption Risks

| Risk | Probability | Impact | Mitigation |
|------|-------------|--------|-----------|
| **User Training Gaps** | Medium | Medium | • Comprehensive user documentation<br>• In-app tooltips<br>• Regular training sessions |
| **Resistance to Change** | Medium | Medium | • Early stakeholder involvement<br>• Pilot phase with select users<br>• Incentivize adoption |
| **Customer Portal Low Adoption** | Medium | Low | • Simple, intuitive UI<br>• Email campaigns<br>• Executive sponsorship |

### 19.4 Schedule Risks

| Risk | Probability | Impact | Mitigation |
|------|-------------|--------|-----------|
| **Scope Creep** | High | High | • Strict scope management<br>• Change request process<br>• Phase-based delivery |
| **Resource Constraints** | Medium | High | • Realistic resource planning<br>• Cross-training team members<br>• Outsourcing option for non-core |
| **Third-party Integration Delays** | Medium | Medium | • Early integration testing<br>• Contingency plans<br>• Parallel development of fallbacks |

---

## 20. Appendix

### 20.1 Glossary

| Term | Definition |
|------|-----------|
| **MTTR** | Mean Time To Resolution - average time to fix an incident |
| **MTBF** | Mean Time Between Failures - average time between incidents |
| **SLA** | Service Level Agreement - commitment to service quality metrics |
| **CPE** | Customer Premises Equipment - customer's router/modem |
| **NOC** | Network Operations Center - team monitoring networks |
| **POP** | Point of Presence - NetFly's network location |
| **SNMP** | Simple Network Management Protocol - device monitoring protocol |
| **RTT** | Round Trip Time - latency measurement |
| **Uptime** | Percentage of time service is online |
| **Downtime** | Duration service is offline |
| **Availability** | (Total Time - Downtime) / Total Time × 100% |
| **Bandwidth Capacity** | Maximum allowed data transfer rate |
| **Bandwidth Usage** | Current data transfer rate |
| **Packet Loss** | Percentage of packets not reaching destination |
| **Latency** | Time for data to travel from source to destination |
| **Alert Threshold** | Value that triggers an alert when exceeded |
| **Incident** | Service disruption requiring investigation |
| **Escalation** | Moving alert/incident to higher priority level |

### 20.2 References & Tools

**Monitoring Standards:**
- ISO/IEC 27001 (Information Security)
- ITIL Best Practices (Incident Management)
- RFC 3410 (SNMP Usage)

**Useful Tools:**
- net-snmp - SNMP tools collection
- Grafana - Visualization platform
- Prometheus - Monitoring system
- ELK Stack - Log aggregation
- K6 / JMeter - Performance testing

### 20.3 Attachment List

- [ ] Network Architecture Diagram (Visio/Draw.io)
- [ ] Database Schema (ERD)
- [ ] Wireframes (Figma/Sketch)
- [ ] API Documentation (Swagger)
- [ ] Security Architecture
- [ ] Deployment Guide
- [ ] User Manual
- [ ] Admin Guide
- [ ] API Integration Guide

---

## Document History

| Version | Date | Author | Changes |
|---------|------|--------|---------|
| 1.0 | Sep 8, 2026 | Product Team | Initial version |
| 2.0 | TBD | Product Team | Post-stakeholder review |

---

## Approval Sign-off

| Role | Name | Signature | Date |
|------|------|-----------|------|
| Product Manager | [Name] | [ ] | [ ] |
| Engineering Lead | [Name] | [ ] | [ ] |
| Business Owner | [Name] | [ ] | [ ] |
| Security Officer | [Name] | [ ] | [ ] |

---

**END OF DOCUMENT**

---

**Questions or feedback?** Contact: product-team@netfly.id
